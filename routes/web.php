<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Models\Project;

Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');
Route::get('/og-image/{file}', [ImageController::class, 'og'])->where('file', '.*')->name('og-image');

// Public site. Pages still on 'pages.shell' are not built yet (Phase 2).
Route::view('/', 'pages.shell')->name('page.home');
Route::get('/bauten/{project:id}/{slug?}', fn (Project $project) => view('pages.shell'))->whereNumber('project')->name('page.project');
Route::view('/werkliste', 'pages.shell')->name('page.works');
Route::view('/werkliste/status', 'pages.shell')->name('page.works.status');
Route::view('/werkliste/jahr', 'pages.shell')->name('page.works.year');
Route::view('/werkliste/typ', 'pages.shell')->name('page.works.type');
Route::view('/presse', 'pages.shell')->name('page.press');
Route::view('/buecher', 'pages.shell')->name('page.books');
Route::view('/downloads', 'pages.shell')->name('page.downloads');
Route::view('/kontakt', 'pages.shell')->name('page.contact');
Route::view('/ueber-uns', 'pages.shell')->name('page.about');
Route::view('/jobs', 'pages.shell')->name('page.jobs');
Route::view('/auszeichnungen', 'pages.shell')->name('page.awards');
Route::view('/vortraege', 'pages.shell')->name('page.lectures');

// Dashboard (Vue SPA) — requires authentication
Route::middleware('auth')->group(function () {
	Route::get('/dashboard/{any?}', function () {
		return view('components.layout.app');
	})->where('any', '.*')->name('dashboard');
});

require __DIR__.'/auth.php';
