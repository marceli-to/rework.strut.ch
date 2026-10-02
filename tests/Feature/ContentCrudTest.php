<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Entry;
use App\Models\JobListing;
use App\Models\Media;
use App\Models\News;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
 * Shared admin CRUD (ResourceController) for every content module.
 * [endpoint, model, valid payload factory, required fields, sortable]
 */
dataset('modules', [
	'categories' => ['categories', Category::class, fn () => ['name' => 'Wohnen', 'show_types' => true], ['name'], true],
	'category types' => ['category-types', CategoryType::class, fn () => ['category_id' => Category::factory()->create()->uuid, 'name_singular' => 'Wohnhaus', 'name_plural' => 'Wohnhäuser'], ['category_id', 'name_singular', 'name_plural'], true],
	'projects' => ['projects', Project::class, fn () => ['category_type_id' => CategoryType::factory()->create()->uuid, 'name' => 'Hofwiesenweg', 'location' => 'Winterthur', 'year' => 2017, 'status' => 'executed'], ['category_type_id', 'name', 'location', 'year', 'status'], true],
	'news' => ['news', News::class, fn () => ['title' => '1. Rang', 'date_label' => 'August 2026'], ['title'], false],
	'team' => ['team', TeamMember::class, fn () => ['firstname' => 'Roger', 'lastname' => 'Studerus', 'email' => 'rs@strut.ch'], ['firstname', 'lastname'], true],
	'jobs' => ['jobs', JobListing::class, fn () => ['title' => 'Lehrstelle'], ['title'], true],
	'books' => ['books', Book::class, fn () => ['title' => 'Sky-Frame', 'url' => 'mail@strut.ch'], ['title'], true],
	'entries' => ['entries', Entry::class, fn () => ['type' => 'award', 'title' => 'best architects 16', 'year' => 2016], ['type', 'title', 'year'], false],
]);

beforeEach(function () {
	$this->user = User::factory()->create();
});

it('lists records', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	$model::factory()->count(3)->create();

	$this->actingAs($this->user)
		->getJson("/api/dashboard/$endpoint")
		->assertOk()
		->assertJsonCount(3, 'data');
})->with('modules');

it('creates a record', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	$this->actingAs($this->user)
		->postJson("/api/dashboard/$endpoint", $payload())
		->assertCreated()
		->assertJsonStructure(['data' => ['uuid']]);

	expect($model::count())->toBe(1);
})->with('modules');

it('validates required fields with german messages', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	$response = $this->actingAs($this->user)
		->postJson("/api/dashboard/$endpoint", [])
		->assertUnprocessable()
		->assertJsonValidationErrors($required);

	expect($response->json('errors.' . $required[0] . '.0'))->toContain('ist erforderlich');
})->with('modules');

it('shows and updates a record', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	$record = $model::factory()->create();
	$data = [...$payload(), 'publish' => true];

	$this->actingAs($this->user)->getJson("/api/dashboard/$endpoint/{$record->uuid}")->assertOk();

	$this->actingAs($this->user)
		->putJson("/api/dashboard/$endpoint/{$record->uuid}", $data)
		->assertOk()
		->assertJsonPath('data.publish', true);
})->with('modules');

it('toggles publish', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	$record = $model::factory()->create(['publish' => false]);

	$this->actingAs($this->user)
		->patchJson("/api/dashboard/$endpoint/{$record->uuid}/publish")
		->assertOk()
		->assertJsonPath('data.publish', true);

	expect($record->fresh()->publish)->toBeTrue();
})->with('modules');

it('reorders sortable records', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	if (!$sortable) {
		$this->actingAs($this->user)->patchJson("/api/dashboard/$endpoint/reorder", ['items' => []])->assertStatus(405);
		return;
	}

	[$a, $b] = $model::factory()->count(2)->create();

	$this->actingAs($this->user)
		->patchJson("/api/dashboard/$endpoint/reorder", ['items' => [
			['uuid' => $a->uuid, 'sort_order' => 1],
			['uuid' => $b->uuid, 'sort_order' => 0],
		]])
		->assertOk();

	expect($a->fresh()->sort_order)->toBe(1)->and($b->fresh()->sort_order)->toBe(0);

	$this->actingAs($this->user)
		->patchJson("/api/dashboard/$endpoint/reorder", ['items' => [['uuid' => 'nope', 'sort_order' => 0]]])
		->assertUnprocessable();
})->with('modules');

it('attaches uploaded media on save and deletes it with the record', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	if (in_array($endpoint, ['categories', 'category-types'])) {
		expect(true)->toBeTrue();
		return;
	}

	Storage::fake('public');

	$upload = $this->actingAs($this->user)
		->postJson('/api/dashboard/media/upload', ['profile' => 'document', 'file' => UploadedFile::fake()->create('plan.pdf', 50, 'application/pdf')])
		->json('data');

	$uuid = $this->actingAs($this->user)
		->postJson("/api/dashboard/$endpoint", [...$payload(), 'media' => [[...$upload, 'collection' => 'files']]])
		->assertCreated()
		->assertJsonPath('data.media.0.collection', 'files')
		->json('data.uuid');

	Storage::disk('public')->assertExists('uploads/' . $upload['file']);

	$this->actingAs($this->user)->deleteJson("/api/dashboard/$endpoint/$uuid")->assertNoContent();

	expect($model::count())->toBe(0)->and(Media::count())->toBe(0);
	Storage::disk('public')->assertMissing('uploads/' . $upload['file']);
})->with('modules');

it('requires authentication', function (string $endpoint, string $model, Closure $payload, array $required, bool $sortable) {
	$this->getJson("/api/dashboard/$endpoint")->assertUnauthorized();
})->with('modules');

it('appends new sortable records at the end of their group', function () {
	$type = CategoryType::factory()->create();
	$other = CategoryType::factory()->create();

	$first = Project::factory()->for($type)->create();
	$second = Project::factory()->for($type)->create();
	$elsewhere = Project::factory()->for($other)->create();

	expect([$first->sort_order, $second->sort_order, $elsewhere->sort_order])->toBe([0, 1, 0]);
});

it('refuses to delete a category or type that still has projects', function (string $endpoint, Closure $owner, string $message) {
	$type = CategoryType::factory()->create();
	Project::factory()->count(2)->for($type)->create();
	$uuid = $owner($type);

	$this->actingAs($this->user)
		->deleteJson("/api/dashboard/$endpoint/$uuid")
		->assertUnprocessable()
		->assertJsonPath('message', $message);

	expect(CategoryType::count())->toBe(1)->and(Project::count())->toBe(2);
})->with([
	'category' => ['categories', fn (CategoryType $type) => $type->category->uuid, 'Die Kategorie enthält noch 2 Projekte. Bitte zuerst die Projekte verschieben oder löschen.'],
	'type' => ['category-types', fn (CategoryType $type) => $type->uuid, 'Der Typ enthält noch 2 Projekte. Bitte zuerst die Projekte verschieben oder löschen.'],
]);
