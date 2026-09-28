<?php

namespace App\Http\Controllers\Site;

use App\Actions\Site\GetWorks;
use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Downloads: project documentation per category, Werkliste PDFs, job PDFs.
 */
class DownloadsController extends Controller
{
	public function __invoke(GetWorks $works): View
	{
		return view('pages.downloads', [
			'page' => Page::findByKey('downloads'),
			'categories' => $works->byType(withFiles: true),
			'jobs' => JobListing::published()->orderBy('sort_order')->orderBy('id')->with('files')->get(),
		]);
	}
}
