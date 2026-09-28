<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\BooksController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\DownloadsController;
use App\Http\Controllers\Site\EntryController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\JobsController;
use App\Http\Controllers\Site\PdfController;
use App\Http\Controllers\Site\ProjectController;
use App\Http\Controllers\Site\SeoController;
use App\Http\Controllers\Site\WorksController;

Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');
Route::get('/og-image/{file}', [ImageController::class, 'og'])->where('file', '.*')->name('og-image');

// Public site.
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/', HomeController::class)->name('page.home');
Route::get('/bauten/{project:id}/{slug?}', ProjectController::class)->whereNumber('project')->name('page.project');
Route::get('/werkliste', [WorksController::class, 'status'])->name('page.works');
Route::get('/werkliste/status', [WorksController::class, 'status'])->name('page.works.status');
Route::get('/werkliste/jahr', [WorksController::class, 'year'])->name('page.works.year');
Route::get('/werkliste/typ', [WorksController::class, 'type'])->name('page.works.type');
// Werkliste PDFs and merged project documentation per category (legacy URLs).
Route::get('/werkliste/pdf/{variant}', [PdfController::class, 'works'])
	->whereIn('variant', array_keys(\App\Actions\Site\GetWorksPdf::VARIANTS))
	->name('pdf.works');
Route::get('/download/pdf/{category:id}/{slug?}', [PdfController::class, 'category'])->whereNumber('category')->name('pdf.category');
Route::get('/presse', EntryController::class)->defaults('type', 'press')->name('page.press');
Route::get('/buecher', BooksController::class)->name('page.books');
Route::get('/downloads', DownloadsController::class)->name('page.downloads');
Route::get('/kontakt', ContactController::class)->name('page.contact');
Route::get('/ueber-uns', AboutController::class)->name('page.about');
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
