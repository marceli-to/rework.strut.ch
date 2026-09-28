<?php

use App\Models\JobListing;
use App\Models\Media;
use App\Models\Page;

beforeEach(function () {
	$this->page = Page::factory()->create(['key' => 'jobs', 'text' => '<div>Zur Zeit sind alle unsere Stellen besetzt.</div>']);
});

it('shows the page text when no job is published', function () {
	JobListing::factory()->create(['title' => 'Entwurf', 'publish' => false]);

	$this->get('/jobs')
		->assertOk()
		->assertSee('Zur Zeit sind alle unsere Stellen besetzt.')
		->assertDontSee('Entwurf');
});

it('lists published jobs with lead, info and the PDF link', function () {
	$job = JobListing::factory()->create(['title' => 'Lehrstelle', 'lead' => 'Zeichner/in EFZ', 'info' => '<p>Ab Sommer</p>', 'publish' => true]);
	Media::factory()->create(['mediable_type' => 'job_listing', 'mediable_id' => $job->id, 'collection' => 'files', 'file' => 'stelle.pdf', 'mime_type' => 'application/pdf']);

	$this->get('/jobs')
		->assertSeeInOrder(['Lehrstelle', 'Zeichner/in EFZ', '<p>Ab Sommer</p>', 'href="/storage/uploads/stelle.pdf"'], false)
		->assertDontSee('Zur Zeit sind alle unsere Stellen besetzt.');
});

it('shows the page images, as a lightbox gallery when there are several', function (int $count, string $mode) {
	Media::factory()->count($count)->create(['mediable_type' => 'page', 'mediable_id' => $this->page->id, 'width' => 2000, 'height' => 2022]);

	$response = $this->get('/jobs');

	expect(substr_count($response->getContent(), "data-lightbox=\"{$mode}\""))->toBe($count)
		->and($response->getContent())->toContain('?h=800&amp;fit=max');
})->with([
	[1, 'single'],
	[2, 'gallery'],
]);
