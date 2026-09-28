<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * The fixed set of pages (no store/destroy). Content pages have title, text
 * and images; listing pages only their SEO fields (see PageRequest).
 */
class PageController extends ResourceController
{
	protected string $model = Page::class;
	protected string $resource = PageResource::class;
	protected string $request = PageRequest::class;

	public function index(Request $request)
	{
		return PageResource::collection(Page::sortByKey(Page::with('media')->get()));
	}

	// listing pages are always online
	public function toggle(string $uuid)
	{
		abort_unless($this->find($uuid)->isContent(), 404);

		return parent::toggle($uuid);
	}
}
