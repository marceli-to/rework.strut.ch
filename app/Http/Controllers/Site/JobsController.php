<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Jobs: published job listings, or the page text when there are none.
 */
class JobsController extends Controller
{
	public function __invoke(): View
	{
		return view('pages.jobs', [
			'page' => Page::with('images')->where('key', 'jobs')->firstOrFail(),
			'jobs' => JobListing::published()->orderBy('sort_order')->orderBy('id')->with('files')->get(),
		]);
	}
}
