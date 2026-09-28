<?php

use App\Models\Category;
use App\Models\LegacyMap;
use App\Models\Media;
use App\Models\Project;

it('redirects /bauten to the Werkliste', function () {
	$this->get('/bauten')->assertRedirect('/werkliste')->assertStatus(301);
});

it('redirects old media URLs to the new file at the matching size', function () {
	$image = Media::factory()->create(['file' => 'neu.jpg', 'width' => 2000, 'height' => 1000]);
	$pdf = Media::factory()->create(['file' => 'doku.pdf', 'collection' => 'files', 'mime_type' => 'application/pdf']);
	LegacyMap::create(['legacy_table' => 'project_images', 'legacy_id' => 1, 'model_type' => 'media', 'model_id' => $image->id, 'legacy_file' => '5da8159bc1779_strut.ch_alt.jpg']);
	LegacyMap::create(['legacy_table' => 'project_files', 'legacy_id' => 1, 'model_type' => 'media', 'model_id' => $pdf->id, 'legacy_file' => '65844ab289397_doku.pdf']);

	$this->get('/storage/media/large/5da8159bc1779_strut.ch_alt.jpg')->assertStatus(301)->assertRedirect('/img/uploads/neu.jpg?w=1600&h=800&fit=stretch');
	$this->get('/storage/media/xsmall/5da8159bc1779_strut.ch_alt.jpg')->assertRedirect('/img/uploads/neu.jpg?w=500&h=250&fit=stretch');
	$this->get('/storage/media/5da8159bc1779_strut.ch_alt.jpg')->assertRedirect('/storage/uploads/neu.jpg');
	$this->get('/media/5da8159bc1779_strut.ch_alt.jpg/md')->assertRedirect('/img/uploads/neu.jpg?w=1200&h=600&fit=stretch');
	$this->get('/storage/media/downloads/65844ab289397_doku.pdf')->assertRedirect('/storage/uploads/doku.pdf');
	$this->get('/storage/media/large/unbekannt.jpg')->assertNotFound();
});

it('redirects the old category ids of the merged PDFs', function () {
	$category = Category::factory()->create(['id' => 14]); // imported categories got new ids (legacy 1–3 → 14–16)
	LegacyMap::create(['legacy_table' => 'categories', 'legacy_id' => 1, 'model_type' => 'category', 'model_id' => $category->id]);

	$this->get('/download/pdf/1/wohnen')->assertStatus(301)->assertRedirect(route('pdf.category', [$category->id, 'wohnen']));
	$this->get('/download/pdf/999/nichts')->assertNotFound();
});

it('does not keep the dropped legacy routes', function (string $path) {
	Project::factory()->create(['publish' => true]);

	$this->get($path)->assertNotFound();
})->with(['/bauten/vorschau/1', '/404', '/500', '/artisan/cache']);
