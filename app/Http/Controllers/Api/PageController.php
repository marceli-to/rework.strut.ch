<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Content pages (Über uns, Jobs, Kontakt, Impressum): no store/destroy.
 */
class PageController extends ResourceController
{
	protected string $model = Page::class;
	protected string $resource = PageResource::class;
	protected string $request = PageRequest::class;

	public function index(Request $request)
	{
		return PageResource::collection(Page::sortByKey(Page::content()->get()));
	}
}
