<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Site\DownloadsController;
use App\Http\Controllers\Site\EntryController;
use App\Http\Controllers\Site\JobsController;
use App\Http\Controllers\Site\WorksController;
use App\Models\Project;

Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');
Route::get('/og-image/{file}', [ImageController::class, 'og'])->where('file', '.*')->name('og-image');

// Public site. Pages still on 'pages.shell' are not built yet (Phase 2).
Route::view('/', 'pages.shell')->name('page.home');
Route::get('/bauten/{project:id}/{slug?}', fn (Project $project) => view('pages.shell'))->whereNumber('project')->name('page.project');
Route::get('/werkliste', [WorksController::class, 'status'])->name('page.works');
Route::get('/werkliste/status', [WorksController::class, 'status'])->name('page.works.status');
Route::get('/werkliste/jahr', [WorksController::class, 'year'])->name('page.works.year');
Route::get('/werkliste/typ', [WorksController::class, 'type'])->name('page.works.type');
// Werkliste PDFs (legacy URLs), built in step 5.
Route::get('/werkliste/pdf/{variant}', fn () => abort(404))
	->whereIn('variant', ['gesamt', 'wohnen', 'gewerbe', 'oeffentlich', 'wettbewerb', 'status', 'jahr', 'typ'])
	->name('pdf.works');
// Merged project documentation per category, built in step 5.
Route::get('/download/pdf/{category:id}/{slug?}', fn () => abort(404))->whereNumber('category')->name('pdf.category');
Route::get('/presse', EntryController::class)->defaults('type', 'press')->name('page.press');
Route::view('/buecher', 'pages.shell')->name('page.books');
Route::get('/downloads', DownloadsController::class)->name('page.downloads');
Route::view('/kontakt', 'pages.shell')->name('page.contact');
Route::view('/ueber-uns', 'pages.shell')->name('page.about');
Route::get('/jobs', JobsController::class)->name('page.jobs');
Route::get('/auszeichnungen', EntryController::class)->defaults('type', 'award')->name('page.awards');
Route::get('/vortraege', EntryController::class)->defaults('type', 'lecture')->name('page.lectures');

// Dashboard (Vue SPA) — requires authentication
Route::middleware('auth')->group(function () {
	Route::get('/dashboard/{any?}', function () {
		return view('components.layout.app');
	})->where('any', '.*')->name('dashboard');
});

require __DIR__.'/auth.php';
