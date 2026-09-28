<?php

use App\Models\Category;
use App\Models\CategoryType;
use App\Models\GridItem;
use App\Models\GridRow;
use App\Models\Media;
use App\Models\Project;
use App\Support\GridContext;

beforeEach(function () {
	$type = CategoryType::factory()->for(Category::factory())->create(['name_singular' => 'Wohnhaus']);
	[$this->a, $this->b, $this->c] = collect(['Alpha', 'Beta', 'Gamma'])->map(fn ($name, $i) => Project::factory()->for($type)->create([
		'name' => $name, 'location' => 'Winterthur', 'publish' => true, 'sort_order' => $i,
	]))->all();
});

function image(Project $project, string $file): Media
{
	return Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id, 'file' => $file, 'width' => 1600, 'height' => 1067]);
}

it('redirects to the canonical slug and hides unpublished projects', function () {
	$this->get("/bauten/{$this->a->id}")->assertRedirect($this->a->url)->assertStatus(301);
	$this->get("/bauten/{$this->a->id}/alter-slug")->assertRedirect($this->a->url);
	$this->get($this->a->url)->assertOk()->assertSee('Alpha, Winterthur')->assertSee('Wohnhaus');

	$this->a->update(['publish' => false]);
	$this->get($this->a->url)->assertNotFound();
});

it('keeps projects without a detail page reachable', function () {
	$this->b->update(['has_detail' => false]);

	$this->get($this->b->url)->assertOk();
});

it('browses in menu order and wraps around', function () {
	$this->get($this->a->url)->assertSeeInOrder(['href="' . $this->c->url . '"', 'href="' . $this->b->url . '"'], false);
	$this->get($this->c->url)->assertSeeInOrder(['href="' . $this->b->url . '"', 'href="' . $this->a->url . '"'], false);
});

it('fills the grid in position order without gaps, like legacy', function () {
	$row = GridRow::factory()->create(['gridable_id' => $this->a->id, 'layout' => '2fr']);
	GridItem::factory()->create(['grid_row_id' => $row->id, 'position' => 1, 'media_id' => image($this->a, 'rechts.jpg')->id]);

	$columns = GridContext::for('project')->fill('2fr', $row->items);

	expect($columns[0]['cells'][0]['item']?->media->file)->toBe('rechts.jpg')
		->and($columns[1]['filled'])->toBeFalse();

	$this->get($this->a->url)
		->assertSee('data-lightbox="gallery"', false)
		->assertSee('/img/uploads/rechts.jpg?w=1200&amp;h=800&amp;fit=stretch', false);
});

it('shows description, info and downloads in the info panel', function () {
	$this->a->update(['description' => '<p>Beschreibung</p>', 'info' => '<p>Bauherrschaft</p>']);
	Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $this->a->id, 'collection' => 'files', 'file' => 'doku.pdf', 'mime_type' => 'application/pdf']);

	$this->get($this->a->url)
		->assertSee('aria-controls="project-info"', false)
		->assertSeeInOrder(['<p>Beschreibung</p>', '<p>Bauherrschaft</p>', 'href="/storage/uploads/doku.pdf"'], false);
});
