<?php

use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Models\CategoryType;
use App\Models\GridRow;
use App\Models\Media;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
	$this->user = User::factory()->create();
});

it('generates the slug like the legacy site', function () {
	$project = Project::factory()->create([
		'name' => 'Bärenhöhle, Kindertagesstätte',
		'location' => 'Frauenfeld',
		'year' => 2017,
	]);

	expect($project->slug)->toBe('baerenhoehle-kindertagesstaette-frauenfeld-2017');
});

it('keeps the slug stable when the name changes', function () {
	$project = Project::factory()->create(['name' => 'Hofwiesenweg', 'location' => 'Winterthur', 'year' => 2017]);

	$project->update(['name' => 'Hofwiesenweg neu']);

	expect($project->fresh()->slug)->toBe('hofwiesenweg-winterthur-2017');
});

it('makes duplicate slugs unique instead of failing', function () {
	$a = Project::factory()->create(['name' => 'Haus A', 'location' => 'Winterthur', 'year' => 2015]);
	$b = Project::factory()->create(['name' => 'Haus A', 'location' => 'Winterthur', 'year' => 2015]);

	expect($a->slug)->toBe('haus-a-winterthur-2015')
		->and($b->slug)->toBe('haus-a-winterthur-2015-2');
});

it('stores the category type from its uuid and returns enum values', function () {
	$type = CategoryType::factory()->create();

	$this->actingAs($this->user)
		->postJson('/api/dashboard/projects', [
			'category_type_id' => $type->uuid,
			'name' => 'Sky-Frame',
			'location' => 'Frauenfeld',
			'year' => 2014,
			'status' => 'executed',
			'competition' => 'first_prize',
		])
		->assertCreated()
		->assertJsonPath('data.category_type_id', $type->uuid)
		->assertJsonPath('data.status', 'executed')
		->assertJsonPath('data.status_label', 'Ausgeführt')
		->assertJsonPath('data.competition', 'first_prize');

	$project = Project::first();
	expect($project->category_type_id)->toBe($type->id)
		->and($project->status)->toBe(ProjectStatus::Executed)
		->and($project->competition)->toBe(Competition::FirstPrize);
});

it('rejects unknown status and competition values', function () {
	$this->actingAs($this->user)
		->postJson('/api/dashboard/projects', [
			'category_type_id' => CategoryType::factory()->create()->uuid,
			'name' => 'X',
			'location' => 'Y',
			'year' => 2020,
			'status' => 'Ausgeführt',
			'competition' => '3. Preis',
		])
		->assertUnprocessable()
		->assertJsonValidationErrors(['status', 'competition']);
});

it('lists projects in category, type and project order', function () {
	$type = CategoryType::factory()->create();
	$second = Project::factory()->for($type)->create(['name' => 'Second', 'sort_order' => 1]);
	$first = Project::factory()->for($type)->create(['name' => 'First', 'sort_order' => 0]);

	$this->actingAs($this->user)
		->getJson('/api/dashboard/projects')
		->assertOk()
		->assertJsonPath('data.0.uuid', $first->uuid)
		->assertJsonPath('data.1.uuid', $second->uuid);
});

it('deletes grid rows and media together with the project', function () {
	$project = Project::factory()->create();
	$media = Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id]);
	$row = GridRow::factory()->create(['gridable_type' => 'project', 'gridable_id' => $project->id]);
	$row->items()->create(['position' => 0, 'media_id' => $media->id]);

	$this->actingAs($this->user)
		->deleteJson("/api/dashboard/projects/{$project->uuid}")
		->assertNoContent();

	expect(GridRow::count())->toBe(0)
		->and(Media::count())->toBe(0)
		->and(\App\Models\GridItem::count())->toBe(0);
});

it('provides select options for admin forms', function () {
	CategoryType::factory()->create();

	$this->actingAs($this->user)
		->getJson('/api/dashboard/options')
		->assertOk()
		->assertJsonCount(3, 'status')
		->assertJsonPath('status.0.label', 'Ausgeführt')
		->assertJsonCount(3, 'entry_type')
		->assertJsonCount(1, 'category_types');
});
