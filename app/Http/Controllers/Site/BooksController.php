<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Bücher (masonry).
 */
class BooksController extends Controller
{
	public function __invoke(): View
	{
		return view('pages.books', [
			'page' => Page::findByKey('books'),
			'books' => Book::published()->orderBy('sort_order')->orderBy('id')->with('images')->get(),
		]);
	}
}
