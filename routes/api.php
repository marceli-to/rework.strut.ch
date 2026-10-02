<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CategoryTypeController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\GridController;
use App\Http\Controllers\Api\JobListingController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\OptionsController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\UserController;

/**
 * Standard admin endpoints of a ResourceController.
 */
$resource = function (string $prefix, string $controller, array $only = ['index', 'store', 'reorder', 'show', 'update', 'toggle', 'destroy']) {
	Route::controller($controller)->prefix($prefix)->group(function () use ($only) {
		in_array('index', $only) && Route::get('/', 'index');
		in_array('store', $only) && Route::post('/', 'store');
		in_array('reorder', $only) && Route::patch('/reorder', 'reorder');
		in_array('show', $only) && Route::get('/{uuid}', 'show');
		in_array('update', $only) && Route::put('/{uuid}', 'update');
		in_array('toggle', $only) && Route::patch('/{uuid}/publish', 'toggle');
		in_array('destroy', $only) && Route::delete('/{uuid}', 'destroy');
	});
};

Route::prefix('dashboard')
	->middleware(['web', 'auth'])
	->group(function () use ($resource) {

		Route::get('/options', OptionsController::class);

		$resource('projects', ProjectController::class);
		$resource('categories', CategoryController::class);
		$resource('category-types', CategoryTypeController::class);
		$resource('pages', PageController::class, ['index', 'show', 'update', 'toggle']);
		$resource('news', NewsController::class, ['index', 'store', 'show', 'update', 'toggle', 'destroy']);
		$resource('team', TeamMemberController::class);
		$resource('jobs', JobListingController::class);
		$resource('books', BookController::class);
		$resource('entries', EntryController::class, ['index', 'store', 'show', 'update', 'toggle', 'destroy']);

		Route::controller(GridController::class)
			->prefix('grids/{context}/{owner}')
			->whereIn('context', array_keys(config('grids.contexts')))
			->group(function () {
				Route::get('/', 'show');
				Route::get('/options', 'options');
				Route::post('/rows', 'storeRow');
				Route::patch('/rows/reorder', 'reorderRows');
				Route::patch('/items/move', 'moveItem');
				Route::put('/rows/{row}', 'updateRow');
				Route::delete('/rows/{row}', 'destroyRow');
				Route::put('/rows/{row}/items/{position}', 'setItem')->whereNumber('position');
				Route::delete('/rows/{row}/items/{position}', 'destroyItem')->whereNumber('position');
			});

		Route::controller(MediaController::class)
			->prefix('media')
			->group(function () {
				Route::post('/upload', 'upload');
				Route::patch('/reorder', 'reorder');
				Route::put('/{media}', 'update');
				Route::delete('/{media}', 'destroy');
				Route::patch('/{media}/og', 'og');
				Route::patch('/{media}/crop', 'crop');
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
