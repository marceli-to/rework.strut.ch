<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;
use App\Models\Page;

class PageRequest extends ContentRequest
{
	protected function fields(): array
	{
		$seo = ['meta_description' => 'nullable|string|max:255'];

		// listing pages: only the SEO fields (+ OG image via the media rules)
		if (!Page::where('uuid', $this->route('uuid'))->first()?->isContent()) {
			return $seo;
		}

		return [
			'title' => 'required|string|max:255',
			'text' => 'nullable|string',
			'publish' => 'boolean',
			...$seo,
		];
	}

	public function attributes(): array
	{
		return ['title' => 'Titel', 'text' => 'Text', 'meta_description' => 'Meta Description'];
	}
}
