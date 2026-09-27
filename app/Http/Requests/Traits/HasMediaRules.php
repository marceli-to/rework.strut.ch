<?php

namespace App\Http\Requests\Traits;

trait HasMediaRules
{
	protected function mediaRules(): array
	{
		return [
			'media' => 'nullable|array',
			'media.*.uuid' => 'required|string',
			'media.*.file' => 'required|string',
			'media.*.original_name' => 'required|string',
			'media.*.mime_type' => 'required|string',
			'media.*.size' => 'required|integer',
			'media.*.width' => 'nullable|integer',
			'media.*.height' => 'nullable|integer',
			'media.*.alt' => 'nullable|string|max:255',
			'media.*.caption' => 'nullable|string|max:255',
			'media.*.crop' => 'nullable|array',
			'media.*.variant' => 'sometimes|in:desktop,mobile',
		];
	}
}
