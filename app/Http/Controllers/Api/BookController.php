<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\BookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class BookController extends ResourceController
{
	protected string $model = Book::class;
	protected string $resource = BookResource::class;
	protected string $request = BookRequest::class;
}
