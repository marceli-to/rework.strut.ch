<?php

use App\Models\GridItem;
use App\Models\GridRow;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
	$this->user = User::factory()->create();
	$this->project = Project::factory()->published()->create();
	$this->home = Page::factory()->home()->create();
});

function imageOf(Project $project): Media
{
	return Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id]);
}

function projectGrid(Project $project, string $path = ''): string
{
	return "/api/dashboard/grids/project/{$project->uuid}$path";
}

const HOME_GRID = '/api/dashboard/grids/home/home';

/*
 * Config + structure (both contexts share one endpoint)
 */

it('returns the context config and rows', function (string $context) {
	$url = $context === 'project' ? projectGrid($this->project) : HOME_GRID;

	$response = $this->actingAs($this->user)->getJson($url)->assertOk();

	expect($response->json('config.key'))->toBe($context)
		->and($response->json('rows'))->toBe([]);

	if ($context === 'project') {
		expect($response->json('config.layouts'))->toBeList()->toHaveCount(7)
			->and($response->json('config.accepts_news'))->toBeFalse();
	} else {
		expect($response->json('config.layouts'))->toBeList()->toHaveCount(12)
			->and(collect($response->json('config.areas'))->pluck('key')->all())->toBe(['highlight', 'main'])
			->and($response->json('config.accepts_news'))->toBeTrue();
	}
})->with(['project', 'home']);

it('404s for unknown contexts and owners', function () {
	$this->actingAs($this->user)->getJson('/api/dashboard/grids/nope/x')->assertNotFound();
	$this->actingAs($this->user)->getJson('/api/dashboard/grids/project/unknown-uuid')->assertNotFound();
});

it('requires authentication', function () {
	$this->getJson(HOME_GRID)->assertUnauthorized();
});

/*
 * Rows
 */

it('prepends rows with layouts allowed in the context', function () {
	$first = $this->actingAs($this->user)->postJson(projectGrid($this->project, '/rows'), ['area' => 'main', 'layout' => '2fr'])->assertCreated();
	$this->actingAs($this->user)->postJson(projectGrid($this->project, '/rows'), ['area' => 'main', 'layout' => '1fr-1fr_stacked'])
		->assertCreated()
		->assertJsonPath('data.sort_order', 0);
	expect(GridRow::where('uuid', $first->json('data.uuid'))->value('sort_order'))->toBe(1);

	// home-only layout in the project grid
	$this->actingAs($this->user)->postJson(projectGrid($this->project, '/rows'), ['area' => 'main', 'layout' => '3fr'])
		->assertJsonValidationErrors('layout');

	// unknown area
	$this->actingAs($this->user)->postJson(projectGrid($this->project, '/rows'), ['area' => 'highlight', 'layout' => '2fr'])
		->assertJsonValidationErrors('area');

	expect($this->project->gridRows()->count())->toBe(2);
});

it('allows only one highlight row on the homepage', function () {
	$this->actingAs($this->user)->postJson(HOME_GRID . '/rows', ['area' => 'highlight', 'layout' => 'slideshow'])->assertCreated();
	$this->actingAs($this->user)->postJson(HOME_GRID . '/rows', ['area' => 'highlight', 'layout' => 'slideshow'])->assertJsonValidationErrors('area');
	$this->actingAs($this->user)->postJson(HOME_GRID . '/rows', ['area' => 'main', 'layout' => 'slideshow'])->assertJsonValidationErrors('layout');
});

it('reorders rows of the owner only', function () {
	[$a, $b] = GridRow::factory()->count(2)->sequence(['sort_order' => 0], ['sort_order' => 1])
		->create(['gridable_id' => $this->project->id]);
	$foreign = GridRow::factory()->create();

	$this->actingAs($this->user)->patchJson(projectGrid($this->project, '/rows/reorder'), ['items' => [
		['uuid' => $a->uuid, 'sort_order' => 1],
		['uuid' => $b->uuid, 'sort_order' => 0],
	]])->assertOk();

	expect($a->fresh()->sort_order)->toBe(1)->and($b->fresh()->sort_order)->toBe(0);

	$this->actingAs($this->user)->patchJson(projectGrid($this->project, '/rows/reorder'), ['items' => [
		['uuid' => $foreign->uuid, 'sort_order' => 0],
	]])->assertUnprocessable();
});

it('changing the layout keeps fitting items and drops the rest', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id, 'layout' => '1fr_sm_lg-1fr_lg_sm']);
	foreach (range(0, 3) as $position) {
		$row->items()->create(['position' => $position, 'media_id' => imageOf($this->project)->id]);
	}

	$this->actingAs($this->user)
		->putJson(projectGrid($this->project, "/rows/{$row->uuid}"), ['layout' => '2fr'])
		->assertOk()
		->assertJsonPath('data.layout', '2fr')
		->assertJsonCount(2, 'data.items');
});

it('deletes a row with its items', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id]);
	$row->items()->create(['position' => 0, 'media_id' => imageOf($this->project)->id]);

	$this->actingAs($this->user)->deleteJson(projectGrid($this->project, "/rows/{$row->uuid}"))->assertNoContent();

	expect(GridRow::count())->toBe(0)->and(GridItem::count())->toBe(0)->and(Media::count())->toBe(1);
});

it('does not touch rows of another owner', function () {
	$foreign = GridRow::factory()->create();

	$this->actingAs($this->user)->deleteJson(projectGrid($this->project, "/rows/{$foreign->uuid}"))->assertNotFound();
	expect(GridRow::count())->toBe(1);
});

/*
 * Items — project context
 */

it('places only the project\'s own media in the project grid', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id, 'layout' => '2fr']);
	$own = imageOf($this->project);
	$foreign = imageOf(Project::factory()->published()->create());

	$this->actingAs($this->user)
		->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/1"), ['media_id' => $own->uuid])
		->assertOk()
		->assertJsonPath('data.items.0.position', 1)
		->assertJsonPath('data.items.0.type', 'image');

	$this->actingAs($this->user)
		->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/0"), ['media_id' => $foreign->uuid])
		->assertJsonValidationErrors('media_id');
});

it('validates positions against the layout', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id, 'layout' => '1fr_lg-1fr_sm_lg']); // 3 slots (+ spacer)

	$this->actingAs($this->user)
		->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/2"), ['media_id' => imageOf($this->project)->uuid])
		->assertOk();

	$this->actingAs($this->user)
		->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/3"), ['media_id' => imageOf($this->project)->uuid])
		->assertJsonValidationErrors('position');
});

it('replaces the item in an occupied slot', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id]);
	$first = imageOf($this->project);
	$second = imageOf($this->project);

	$this->actingAs($this->user)->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/0"), ['media_id' => $first->uuid]);
	$this->actingAs($this->user)->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/0"), ['media_id' => $second->uuid]);

	expect($row->items()->count())->toBe(1)->and($row->items()->first()->media_id)->toBe($second->id);
});

it('rejects news in the project grid', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id]);

	$this->actingAs($this->user)
		->putJson(projectGrid($this->project, "/rows/{$row->uuid}/items/0"), ['news_id' => News::factory()->create()->uuid])
		->assertJsonValidationErrors('news_id');
});

it('removes the grid item when its media is deleted', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id]);
	$media = imageOf($this->project);
	$row->items()->create(['position' => 0, 'media_id' => $media->id]);

	$this->actingAs($this->user)->deleteJson("/api/dashboard/media/{$media->uuid}")->assertNoContent();

	expect(GridItem::count())->toBe(0);
});

/*
 * Items — home context
 */

it('places media of any published project on the homepage', function () {
	$row = GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $this->home->id, 'layout' => '2fr-1fr']);
	$published = imageOf($this->project);
	$draft = imageOf(Project::factory()->create(['publish' => false]));

	$this->actingAs($this->user)
		->putJson(HOME_GRID . "/rows/{$row->uuid}/items/0", ['media_id' => $published->uuid])
		->assertOk();

	$this->actingAs($this->user)
		->putJson(HOME_GRID . "/rows/{$row->uuid}/items/1", ['media_id' => $draft->uuid])
		->assertJsonValidationErrors('media_id');
});

it('accepts news only in news-capable homepage cells', function () {
	$row = GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $this->home->id, 'layout' => '2fr-1fr']);
	$news = News::factory()->create();

	// position 0 is the wide cell
	$this->actingAs($this->user)
		->putJson(HOME_GRID . "/rows/{$row->uuid}/items/0", ['news_id' => $news->uuid])
		->assertJsonValidationErrors('news_id');

	$this->actingAs($this->user)
		->putJson(HOME_GRID . "/rows/{$row->uuid}/items/1", ['news_id' => $news->uuid])
		->assertOk()
		->assertJsonPath('data.items.0.type', 'news')
		->assertJsonPath('data.items.0.news.title', $news->title);
});

it('takes any number of items in the highlight slideshow', function () {
	$row = GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $this->home->id, 'area' => 'highlight', 'layout' => 'slideshow']);

	foreach (range(0, 7) as $position) {
		$this->actingAs($this->user)
			->putJson(HOME_GRID . "/rows/{$row->uuid}/items/$position", ['media_id' => imageOf($this->project)->uuid])
			->assertOk();
	}

	expect($row->items()->count())->toBe(8);
});

it('moves items between rows by swapping', function () {
	$a = GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $this->home->id, 'layout' => '3fr']);
	$b = GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $this->home->id, 'layout' => '3fr']);
	$x = $a->items()->create(['position' => 0, 'media_id' => imageOf($this->project)->id]);
	$y = $b->items()->create(['position' => 2, 'news_id' => News::factory()->create()->id]);

	$this->actingAs($this->user)
		->patchJson(HOME_GRID . '/items/move', ['from_row' => $a->uuid, 'from_position' => 0, 'to_row' => $b->uuid, 'to_position' => 2])
		->assertOk()
		->assertJsonCount(2, 'rows');

	expect($x->fresh()->only('grid_row_id', 'position'))->toBe(['grid_row_id' => $b->id, 'position' => 2])
		->and($y->fresh()->only('grid_row_id', 'position'))->toBe(['grid_row_id' => $a->id, 'position' => 0]);
});

it('refuses to move news into a cell that does not take news', function () {
	$row = GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $this->home->id, 'layout' => '2fr-1fr']);
	$row->items()->create(['position' => 1, 'news_id' => News::factory()->create()->id]);

	$this->actingAs($this->user)
		->patchJson(HOME_GRID . '/items/move', ['from_row' => $row->uuid, 'from_position' => 1, 'to_row' => $row->uuid, 'to_position' => 0])
		->assertJsonValidationErrors('to_position');
});

it('offers media of the context scope and marks placed ones', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->project->id]);
	$placed = imageOf($this->project);
	imageOf($this->project);
	$row->items()->create(['position' => 0, 'media_id' => $placed->id]);
	News::factory()->create();

	$project = $this->actingAs($this->user)->getJson(projectGrid($this->project, '/options'))->assertOk();
	expect($project->json('media'))->toHaveCount(2)
		->and(collect($project->json('media'))->firstWhere('uuid', $placed->uuid)['placed'])->toBeTrue()
		->and($project->json('news'))->toBe([]);

	$home = $this->actingAs($this->user)->getJson(HOME_GRID . '/options')->assertOk();
	expect($home->json('media'))->toHaveCount(2)
		->and($home->json('media.0.project.title'))->toBe($this->project->full_title)
		->and($home->json('news'))->toHaveCount(1);
});
