<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AtelierController;
use App\Http\Controllers\ContactController;

Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');
Route::get('/og-image/{file}', [ImageController::class, 'og'])->where('file', '.*')->name('og-image');

Route::get('/', LandingController::class)->name('page.landing');

Route::prefix('projekte')->group(function () {
	Route::get('/auswahl', [ProjectController::class, 'featured'])->name('page.projects');
	Route::get('/werkliste', [ProjectController::class, 'worklist'])->name('page.projects.worklist');
	Route::get('/auswahl/{project:slug}/bilder', [ProjectController::class, 'images'])->defaults('context', 'featured')->name('page.project.featured.images');
	Route::get('/auswahl/{project:slug}/text', [ProjectController::class, 'text'])->defaults('context', 'featured')->name('page.project.featured.text');
	Route::get('/werkliste/{project:slug}/bilder', [ProjectController::class, 'images'])->defaults('context', 'worklist')->name('page.project.worklist.images');
	Route::get('/werkliste/{project:slug}/text', [ProjectController::class, 'text'])->defaults('context', 'worklist')->name('page.project.worklist.text');
});

Route::prefix('atelier')->group(function () {
	Route::get('/profil', [AtelierController::class, 'profile'])->name('page.atelier.profile');
	Route::get('/team', [AtelierController::class, 'team'])->name('page.atelier.team');
	Route::get('/jobs', [AtelierController::class, 'jobs'])->name('page.atelier.jobs');
});

Route::get('/kontakt', ContactController::class)->name('page.contact');

// Dashboard (Vue SPA) — requires authentication
Route::middleware('auth')->group(function () {
	Route::get('/dashboard/{any?}', function () {
		return view('components.layout.app');
	})->where('any', '.*')->name('dashboard');
});

require __DIR__.'/auth.php';
