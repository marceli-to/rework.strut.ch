<?php

use App\Actions\Site\GetWorksPdf;
use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\LegacyMap;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Support\Facades\File;

beforeEach(function () {
	$this->category = Category::factory()->create(['name' => 'Wohnen', 'show_types' => true]);
	LegacyMap::create(['legacy_table' => 'categories', 'legacy_id' => 1, 'model_type' => 'category', 'model_id' => $this->category->id]);
	$this->type = CategoryType::factory()->for($this->category)->create(['name_singular' => 'Wohnhaus', 'name_plural' => 'Wohnhäuser']);
	Project::factory()->for($this->type)->create(['name' => 'Alt', 'location' => 'Winterthur', 'year' => 2010, 'status' => ProjectStatus::Executed, 'publish' => true]);
	Project::factory()->for($this->type)->create(['name' => 'Neu', 'location' => 'Brütten', 'year' => 2020, 'status' => ProjectStatus::Study, 'competition' => Competition::FirstPrize, 'publish' => true]);
	Project::factory()->for($this->type)->create(['name' => 'Entwurf', 'publish' => false]);
});

it('builds the legacy lines per variant', function (string $variant, array $expected) {
	$sections = app(GetWorksPdf::class)->execute($variant)['sections'];

	expect(collect($sections)->map(fn ($section) => [$section['title'], collect($section['groups'])->flatMap(fn ($g) => array_filter([$g['heading'], ...$g['lines']]))->all()])->all())
		->toBe($expected);
})->with([
	['gesamt', [
		['Wohnen', ['Wohnhäuser', 'Neu, Brütten – 2020', 'Alt, Winterthur – 2010']],
		['Wettbewerbe', ['1. Preis', 'Neu, Brütten – Wohnhaus, 2020']],
	]],
	['wohnen', [['Wohnhäuser', ['Neu, Brütten – 2020', 'Alt, Winterthur – 2010']]]],
	['wettbewerb', [['1. Preis', ['Neu, Brütten – Wohnhaus, 2020']]]],
	['status', [['Ausgeführt', ['Alt, Winterthur – Wohnhaus, 2010']], ['Studie', ['Neu, Brütten – Wohnhaus, 2020']]]],
	['jahr', [['2020', ['Neu, Brütten – Wohnhaus, Studie']], ['2010', ['Alt, Winterthur – Wohnhaus, ausgeführt']]]],
	['typ', [['Wohnen', ['Wohnhäuser', 'Neu, Brütten – 2020, Studie', 'Alt, Winterthur – 2010, ausgeführt']]]],
]);

it('streams each Werkliste PDF inline with the legacy file name', function () {
	$this->get('/werkliste/pdf/jahr')
		->assertOk()
		->assertHeader('Content-Type', 'application/pdf')
		->assertHeader('Content-Disposition', 'inline; filename="strut.ch-werkliste-jahr-' . now()->format('d.m.Y') . '.pdf"');

	$this->get('/werkliste/pdf/unbekannt')->assertNotFound();
});

it('merges the project PDFs of a category, or 404 without any', function () {
	$this->get("/download/pdf/{$this->category->id}/wohnen")->assertNotFound();

	$project = Project::where('name', 'Alt')->first();
	$pdf = new TCPDF();
	$pdf->AddPage();
	$pdf->Write(0, 'Doku');
	File::ensureDirectoryExists(storage_path('app/public/uploads'));
	$pdf->Output(storage_path('app/public/uploads/merge-test.pdf'), 'F');
	Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id, 'collection' => 'files', 'file' => 'merge-test.pdf', 'mime_type' => 'application/pdf']);

	try {
		$response = $this->get("/download/pdf/{$this->category->id}/wohnen")->assertOk()->assertHeader('Content-Type', 'application/pdf');
		expect(substr($response->getContent(), 0, 5))->toBe('%PDF-')
			->and($response->headers->get('Content-Disposition'))->toStartWith('inline; filename="strut.ch-Projektdokumentation-Wohnen-');
	} finally {
		File::delete(storage_path('app/public/uploads/merge-test.pdf'));
		File::delete(File::glob(storage_path("app/pdf-cache/category-{$this->category->id}-*.pdf")));
	}
});
