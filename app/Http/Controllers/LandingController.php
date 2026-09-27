<?php

namespace App\Http\Controllers;

use App\Models\LandingSlide;

class LandingController extends Controller
{
	public function __invoke()
	{
		$slides = LandingSlide::published()
			->with('media')
			->orderBy('sort_order')
			->get()
			->shuffle();

		return view('pages.landing', compact('slides'));
	}
}
