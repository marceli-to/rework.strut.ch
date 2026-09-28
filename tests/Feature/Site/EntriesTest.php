<?php

use App\Actions\Site\GetEntries;
use App\Enums\EntryType;
use App\Models\Entry;
use App\Models\Media;
use App\Models\Page;
use App\Models\Project;

beforeEach(function () {
	foreach (['press', 'awards', 'lectures'] as $key) {
		Page::factory()->create(['key' => $key, 'meta_description' => "Beschreibung {$key}"]);
	}
});

it('splits the years into three columns of whole years, like the legacy partition', function () {
	foreach ([2024, 2024, 2023, 2022, 2021, 2020, 2019, 2018] as $year) {
		Entry::factory()->create(['type' => EntryType::Award, 'year' => $year, 'publish' => true]);
	}

	$columns = app(GetEntries::class)->execute(EntryType::Award);

	expect($columns->map(fn ($years) => $years->keys()->all())->all())
		->toBe([[2024, 2023, 2022], [2021, 2020, 2019], [2018]])
		->and($columns[0][2024])->toHaveCount(2);
});

it('lists only published entries of the page type', function (string $url, EntryType $type) {
	Entry::factory()->create(['type' => $type, 'title' => 'Sichtbar', 'publish' => true]);
	Entry::factory()->create(['type' => $type, 'title' => 'Entwurf', 'publish' => false]);
	$other = $type === EntryType::Press ? EntryType::Award : EntryType::Press;
	Entry::factory()->create(['type' => $other, 'title' => 'Anderer Typ', 'publish' => true]);

	$this->get($url)
		->assertOk()
		->assertSee('<h1', false)
		->assertSee($type->label())
		->assertSee('Sichtbar')
		->assertDontSee('Entwurf')
		->assertDontSee('Anderer Typ');
})->with([
	['/presse', EntryType::Press],
	['/auszeichnungen', EntryType::Award],
	['/vortraege', EntryType::Lecture],
]);

it('links the title to the PDF, else to the URL, and adds the project to press entries', function () {
	$project = Project::factory()->create(['name' => 'Werkhof', 'location' => 'Winterthur', 'year' => 2023]);
	$pdf = Entry::factory()->create(['title' => 'Mit PDF', 'description' => 'in TEC21', 'url' => 'https://example.com/nicht', 'project_id' => $project->id, 'publish' => true]);
	Media::factory()->create(['mediable_type' => 'entry', 'mediable_id' => $pdf->id, 'collection' => 'files', 'file' => 'artikel.pdf', 'mime_type' => 'application/pdf']);
	Entry::factory()->create(['title' => 'Mit URL', 'url' => 'https://example.com/artikel', 'publish' => true]);

	$this->get('/presse')
		->assertSee('href="/storage/uploads/artikel.pdf"', false)
		->assertDontSee('https://example.com/nicht')
		->assertSee('href="https://example.com/artikel"', false)
		->assertSee('in TEC21, Werkhof Winterthur (2023)');
});
