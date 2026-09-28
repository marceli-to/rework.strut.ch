<?php

use App\Models\CategoryType;
use App\Models\JobListing;
use App\Models\Media;
use App\Models\Page;
use App\Models\Project;

beforeEach(function () {
	Page::factory()->create(['key' => 'downloads']);
});

function pdfFor(string $type, int $id, string $file): Media
{
	return Media::factory()->create(['mediable_type' => $type, 'mediable_id' => $id, 'collection' => 'files', 'file' => $file, 'mime_type' => 'application/pdf']);
}

it('lists project PDFs per category and leaves out projects and types without files', function () {
	$type = CategoryType::factory()->create(['name_plural' => 'Wohnhäuser']);
	$empty = CategoryType::factory()->for($type->category)->create(['name_plural' => 'Ohne Dateien']);
	$project = Project::factory()->for($type)->create(['name' => 'Hofwiesenweg', 'location' => 'Winterthur', 'publish' => true]);
	Project::factory()->for($type)->create(['name' => 'Ohne PDF', 'publish' => true, 'has_detail' => false]);
	Project::factory()->for($empty)->create(['publish' => true, 'has_detail' => false]);
	pdfFor('project', $project->id, 'hofwiesenweg.pdf');

	$this->get('/downloads')
		->assertOk()
		->assertSee('Alle ' . $type->category->name)
		->assertSee('href="' . route('pdf.category', [$type->category->id, Str::slug($type->category->name)]) . '"', false)
		->assertSee('Wohnhäuser')
		->assertSee('href="/storage/uploads/hofwiesenweg.pdf"', false)
		->assertDontSee('Ohne PDF')
		->assertDontSee('Ohne Dateien');
});

it('links all eight Werkliste PDFs', function () {
	$response = $this->get('/downloads');

	foreach (['gesamt', 'wohnen', 'gewerbe', 'oeffentlich', 'wettbewerb', 'status', 'jahr', 'typ'] as $variant) {
		$response->assertSee('href="' . url("/werkliste/pdf/{$variant}") . '"', false);
	}
});

it('lists published job PDFs, or says that all positions are filled', function () {
	$this->get('/downloads')->assertSee('Zur Zeit sind alle unsere Stellen besetzt.');

	$job = JobListing::factory()->create(['title' => 'Architekt/in', 'publish' => true]);
	JobListing::factory()->create(['title' => 'Entwurf', 'publish' => false]);
	pdfFor('job_listing', $job->id, 'stelle.pdf');

	$this->get('/downloads')
		->assertDontSee('Zur Zeit sind alle unsere Stellen besetzt.')
		->assertSee('href="/storage/uploads/stelle.pdf"', false)
		->assertSee('Architekt/in')
		->assertDontSee('Entwurf');
});
