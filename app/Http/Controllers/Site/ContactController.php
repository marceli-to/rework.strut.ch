<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Kontakt: contact text, Impressum and Datenschutz toggles, map.
 */
class ContactController extends Controller
{
	public function __invoke(): View
	{
		$pages = Page::published()->whereIn('key', ['contact', 'imprint'])->get()->keyBy('key');

		return view('pages.contact', [
			'contact' => $pages->get('contact'),
			'imprint' => $pages->get('imprint'),
			'mapsKey' => config('strut.google_maps_key'),
		]);
	}
}
