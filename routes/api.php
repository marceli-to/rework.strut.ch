<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\AtelierController;
use App\Http\Controllers\Api\KontaktController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\UserController;

Route::prefix('dashboard')
	->middleware(['web', 'auth'])
	->group(function () {

		Route::controller(ProjectController::class)
			->prefix('projects')
			->group(function () {
				Route::get('/', 'index');
				Route::get('/featured', 'featured');
				Route::post('/', 'store');
				Route::get('/{project}', 'show');
				Route::put('/{project}', 'update');
				Route::patch('/{project}/publish', 'toggle');
				Route::patch('/{project}/feature', 'feature');
				Route::delete('/{project}', 'destroy');
			});

		Route::controller(MediaController::class)
			->prefix('media')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/upload', 'upload');
				Route::put('/{media}', 'update');
				Route::delete('/{media}', 'destroy');
				Route::patch('/reorder', 'reorder');
				Route::patch('/{media}/teaser', 'teaser');
				Route::patch('/{media}/og', 'og');
				Route::patch('/{media}/crop', 'crop');
			});

		Route::controller(JobController::class)
			->prefix('jobs')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{job}', 'show');
				Route::put('/{job}', 'update');
				Route::patch('/{job}/publish', 'toggle');
				Route::delete('/{job}', 'destroy');
			});

		Route::controller(TeamController::class)
			->prefix('team')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{member}', 'show');
				Route::put('/{member}', 'update');
				Route::patch('/{member}/publish', 'toggle');
				Route::delete('/{member}', 'destroy');
			});

		Route::controller(TopicController::class)
			->prefix('topics')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{topic}', 'show');
				Route::put('/{topic}', 'update');
				Route::patch('/{topic}/publish', 'toggle');
				Route::delete('/{topic}', 'destroy');
			});

		Route::controller(LandingController::class)
			->prefix('landing')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::patch('/reorder', 'reorder');
				Route::get('/{slide}', 'show');
				Route::put('/{slide}', 'update');
				Route::patch('/{slide}/publish', 'toggle');
				Route::delete('/{slide}', 'destroy');
			});

		Route::controller(AtelierController::class)
			->prefix('atelier')
			->group(function () {
				Route::get('/', 'index');
				Route::get('/{page}', 'show');
				Route::put('/{page}', 'update');
				Route::patch('/{page}/publish', 'toggle');
			});

		Route::controller(KontaktController::class)
			->prefix('contact')
			->group(function () {
				Route::get('/', 'show');
				Route::put('/', 'update');
			});

		Route::controller(SeoController::class)
			->prefix('seo')
			->group(function () {
				Route::get('/', 'show');
				Route::put('/', 'update');
			});

		Route::controller(UserController::class)
			->prefix('users')
			->group(function () {
				Route::get('/', 'index');
				Route::post('/', 'store');
				Route::get('/{user}', 'show');
				Route::put('/{user}', 'update');
				Route::delete('/{user}', 'destroy');
			});

	});
