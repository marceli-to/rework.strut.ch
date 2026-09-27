<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');
Route::get('/og-image/{file}', [ImageController::class, 'og'])->where('file', '.*')->name('og-image');

// Public site: rebuilt in Phase 2
Route::view('/', 'placeholder')->name('page.home');

// Dashboard (Vue SPA) — requires authentication
Route::middleware('auth')->group(function () {
	Route::get('/dashboard/{any?}', function () {
		return view('components.layout.app');
	})->where('any', '.*')->name('dashboard');
});

require __DIR__.'/auth.php';
