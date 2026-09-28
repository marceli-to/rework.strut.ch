<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Response;

/**
 * sitemap.xml and robots.txt (search engines only index production).
 */
class SeoController extends Controller
{
	private const ROUTES = [
		'page.home' => 'home', 'page.works' => 'works', 'page.works.year' => 'works', 'page.works.type' => 'works',
		'page.press' => 'press', 'page.books' => 'books', 'page.downloads' => 'downloads', 'page.about' => 'about',
		'page.jobs' => 'jobs', 'page.awards' => 'awards', 'page.lectures' => 'lectures', 'page.contact' => 'contact',
	];

	public function sitemap(): Response
	{
		$pages = Page::whereIn('key', array_unique(self::ROUTES))->pluck('updated_at', 'key');

		$urls = collect(self::ROUTES)
			->map(fn (string $key, string $route) => ['loc' => route($route), 'lastmod' => $pages[$key] ?? null])
			->values()
			->concat(Project::published()->detailed()->orderBy('id')->get()
				->map(fn (Project $project) => ['loc' => url($project->url), 'lastmod' => $project->updated_at]));

		// The XML declaration is added here: in a Blade view its closing tag would end PHP mode.
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . view('seo.sitemap', ['urls' => $urls])->render();

		return response($xml)->header('Content-Type', 'application/xml');
	}

	public function robots(): Response
	{
		$lines = app()->isProduction()
			? ['User-agent: *', 'Disallow:', '', 'Sitemap: ' . route('sitemap')]
			: ['User-agent: *', 'Disallow: /'];

		return response(implode("\n", $lines) . "\n")->header('Content-Type', 'text/plain');
	}
}
