<?php

use App\Models\Entry;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
	$this->user = User::factory()->create();
});

it('lists only the content pages, in the fixed page order', function () {
	Page::factory()->create(['key' => 'contact']);
	Page::factory()->create(['key' => 'about']);
	Page::factory()->home()->create();
	Page::factory()->create(['key' => 'press']);

	$this->actingAs($this->user)
		->getJson('/api/dashboard/pages')
		->assertOk()
		->assertJsonCount(2, 'data')
		->assertJsonPath('data.0.key', 'about')
		->assertJsonPath('data.1.key', 'contact');
});

it('edits the meta descriptions of the listing pages under SEO', function () {
	Page::factory()->home()->create();
	Page::factory()->create(['key' => 'press', 'title' => 'Presse']);
	Page::factory()->create(['key' => 'about']);

	$this->actingAs($this->user)
		->getJson('/api/dashboard/seo')
		->assertOk()
		->assertJsonCount(2, 'data')
		->assertJsonPath('data.0.key', 'home');

	$this->actingAs($this->user)
		->putJson('/api/dashboard/seo', ['pages' => [
			['key' => 'home', 'meta_description' => 'Strut Architekten Winterthur'],
			['key' => 'press', 'meta_description' => null],
		]])
		->assertOk()
		->assertJsonPath('data.0.meta_description', 'Strut Architekten Winterthur');

	// content pages are edited in their own form
	$this->actingAs($this->user)
		->putJson('/api/dashboard/seo', ['pages' => [['key' => 'about', 'meta_description' => 'x']]])
		->assertJsonValidationErrors('pages.0.key');
});

it('updates a page but cannot create or delete one', function () {
	$page = Page::factory()->create(['key' => 'about']);

	$this->actingAs($this->user)
		->putJson("/api/dashboard/pages/{$page->uuid}", ['title' => 'Über uns', 'meta_description' => 'Büro in Winterthur'])
		->assertOk()
		->assertJsonPath('data.title', 'Über uns')
		->assertJsonPath('data.key', 'about');

	$this->actingAs($this->user)->postJson('/api/dashboard/pages', ['title' => 'X'])->assertStatus(405);
	$this->actingAs($this->user)->deleteJson("/api/dashboard/pages/{$page->uuid}")->assertStatus(405);
});

it('filters entries by type and orders them by year', function () {
	Entry::factory()->create(['type' => 'press', 'year' => 2018]);
	Entry::factory()->create(['type' => 'press', 'year' => 2021]);
	Entry::factory()->create(['type' => 'award', 'year' => 2020]);

	$this->actingAs($this->user)
		->getJson('/api/dashboard/entries?type=press')
		->assertOk()
		->assertJsonCount(2, 'data')
		->assertJsonPath('data.0.year', 2021)
		->assertJsonPath('data.1.year', 2018);
});

it('links a press entry to a project by uuid', function () {
	$project = Project::factory()->create();

	$this->actingAs($this->user)
		->postJson('/api/dashboard/entries', [
			'type' => 'press',
			'project_id' => $project->uuid,
			'title' => 'Siedlung Hofwiesenweg',
			'year' => 2018,
		])
		->assertCreated()
		->assertJsonPath('data.project_id', $project->uuid);

	expect(Entry::first()->project_id)->toBe($project->id);
});

it('accepts a url or an e-mail address as book order link', function () {
	$this->actingAs($this->user)->postJson('/api/dashboard/books', ['title' => 'A', 'url' => 'mail@strut.ch'])->assertCreated();
	$this->actingAs($this->user)->postJson('/api/dashboard/books', ['title' => 'B', 'url' => 'https://example.com'])->assertCreated();
	$this->actingAs($this->user)->postJson('/api/dashboard/books', ['title' => 'C', 'url' => 'nicht gültig'])->assertJsonValidationErrors('url');

	expect(\App\Models\Book::where('title', 'A')->first()->href)->toBe('mailto:mail@strut.ch');
});
