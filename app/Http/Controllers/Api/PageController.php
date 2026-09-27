<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Fixed set of pages: no store/destroy (see routes/api.php).
 */
class PageController extends ResourceController
{
	protected string $model = Page::class;
	protected string $resource = PageResource::class;
	protected string $request = PageRequest::class;

	public function index(Request $request)
	{
		$order = array_flip(array_keys(Page::KEYS));

		return PageResource::collection(Page::all()->sortBy(fn (Page $page) => $order[$page->key] ?? PHP_INT_MAX)->values());
	}
}
