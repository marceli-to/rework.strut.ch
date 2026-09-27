<?php

namespace App\Http\Requests\Content;

use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Http\Requests\ContentRequest;
use App\Models\CategoryType;
use Illuminate\Validation\Rule;

class ProjectRequest extends ContentRequest
{
	protected array $uuidFields = ['category_type_id' => CategoryType::class];

	protected function fields(): array
	{
		return [
			'category_type_id' => 'required|string|exists:category_types,uuid',
			'title' => 'nullable|string|max:255',
			'name' => 'required|string|max:255',
			'location' => 'required|string|max:255',
			'slug' => 'nullable|string|max:255',
			'year' => 'required|integer|min:1900|max:2100',
			'description' => 'nullable|string',
			'info' => 'nullable|string',
			'status' => ['required', Rule::enum(ProjectStatus::class)],
			'competition' => ['nullable', Rule::enum(Competition::class)],
			'has_detail' => 'boolean',
			'meta_description' => 'nullable|string|max:255',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return [
			'category_type_id' => 'Typ',
			'title' => 'Kurztitel',
			'name' => 'Name',
			'location' => 'Ort',
			'slug' => 'Slug',
			'year' => 'Jahr',
			'description' => 'Beschreibung',
			'info' => 'Info',
			'status' => 'Status',
			'competition' => 'Wettbewerb',
			'meta_description' => 'Meta Description',
		];
	}
}
