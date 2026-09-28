<?php

use App\Models\Page;

beforeEach(function () {
	Page::factory()->create(['key' => 'contact', 'title' => 'Kontakt', 'text' => '<p>Neuwiesenstrasse 69</p>']);
	$this->imprint = Page::factory()->create(['key' => 'imprint', 'text' => '<p>Redaktion</p>']);
});

it('shows the contact text, the Impressum and the Datenschutz toggles', function () {
	$this->get('/kontakt')
		->assertOk()
		->assertSee('Neuwiesenstrasse 69')
		->assertSee('aria-controls="impressum"', false)
		->assertSee('<p>Redaktion</p>', false)
		->assertSee('aria-controls="datenschutz"', false)
		->assertSee('Recht auf Auskunft, Löschung, Sperrung');
});

it('leaves out the Impressum when it is not published', function () {
	$this->imprint->update(['publish' => false]);

	$this->get('/kontakt')
		->assertOk()
		->assertDontSee('aria-controls="impressum"', false)
		->assertDontSee('Redaktion');
});

it('passes the Maps key to the map only when one is configured', function (?string $key) {
	config(['strut.google_maps_key' => $key]);

	$response = $this->get('/kontakt')->assertSee('data-map', false);

	$key
		? $response->assertSee('data-map-key="' . $key . '"', false)
		: $response->assertDontSee('data-map-key', false);
})->with([['test-key'], [null]]);
