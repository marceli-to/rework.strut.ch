<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;

class NewsController extends ResourceController
{
	protected string $model = News::class;
	protected string $resource = NewsResource::class;
	protected string $request = NewsRequest::class;
}
