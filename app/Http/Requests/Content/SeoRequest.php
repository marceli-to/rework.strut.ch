<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

/**
 * SEO of a listing page: meta description and OG image (media collection `og`).
 */
class SeoRequest extends ContentRequest
{
	protected function fields(): array
	{
		return [
			'meta_description' => 'nullable|string|max:255',
		];
	}

	public function attributes(): array
	{
		return ['meta_description' => 'Meta Description'];
	}
}
