<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\SeoRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * SEO of the listing pages (Einstellungen → SEO): meta description and OG
 * image. The content pages have theirs in their own form ("Seiten").
 */
class SeoController extends ResourceController
{
	protected string $model = Page::class;
	protected string $resource = PageResource::class;
	protected string $request = SeoRequest::class;
	protected array $indexWith = ['media'];

	public function index(Request $request)
	{
		return PageResource::collection(Page::sortByKey(Page::listing()->with($this->indexWith)->get()));
	}

	protected function find(string $uuid): Model
	{
		return Page::listing()->where('uuid', $uuid)->firstOrFail();
	}
}
