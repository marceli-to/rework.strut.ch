<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Meta descriptions of the listing pages (Einstellungen → SEO).
 */
class SeoController extends Controller
{
	public function show()
	{
		return response()->json(['data' => $this->pages()]);
	}

	public function update(Request $request)
	{
		$data = $request->validate([
			'pages' => 'required|array',
			'pages.*.key' => ['required', 'string', fn ($attribute, $value, $fail) => Page::listing()->where('key', $value)->exists() ?: $fail('Unbekannte Seite.')],
			'pages.*.meta_description' => 'nullable|string|max:255',
		], [], ['pages.*.meta_description' => 'Meta Description']);

		DB::transaction(function () use ($data) {
			foreach ($data['pages'] as $page) {
				Page::where('key', $page['key'])->update(['meta_description' => $page['meta_description'] ?? null]);
			}
		});

		return response()->json(['data' => $this->pages()]);
	}

	protected function pages()
	{
		return Page::sortByKey(Page::listing()->get())->map(fn (Page $page) => [
			'key' => $page->key,
			'title' => $page->title,
			'meta_description' => $page->meta_description,
		]);
	}
}
