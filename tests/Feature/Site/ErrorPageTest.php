<?php

use Illuminate\Support\Facades\Route;

it('answers unknown URLs with the 404 page in the site layout', function () {
	$this->get('/gibt-es-nicht')
		->assertNotFound()
		->assertSee('<title>404 - Seite nicht gefunden - Strut Architekten</title>', false)
		->assertSee('Die gewünschte Seite wurde nicht gefun')
		->assertSee('data-menu', false);
});

it('shows the 500 page when something breaks', function () {
	config(['app.debug' => false]);
	Route::get('/kaputt', fn () => throw new RuntimeException('kaputt'))->middleware('web');

	$this->get('/kaputt')
		->assertStatus(500)
		->assertSee('<title>500 - Fatal error - Strut Architekten</title>', false)
		->assertSee('Es ist ein Fehler aufgetreten.');
});
