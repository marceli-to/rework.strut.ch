<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\TeamMember;
use Illuminate\View\View;

/**
 * Über uns: page text and images, team (masonry).
 */
class AboutController extends Controller
{
	public function __invoke(): View
	{
		return view('pages.about', [
			'page' => Page::published()->with('images')->where('key', 'about')->first(),
			'meta' => Page::findByKey('about'),
			'team' => TeamMember::published()->orderBy('sort_order')->orderBy('id')->with('images')->get(),
		]);
	}
}
