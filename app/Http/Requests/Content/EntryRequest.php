<?php

namespace App\Http\Requests\Content;

use App\Enums\EntryType;
use App\Http\Requests\ContentRequest;
use App\Models\Project;
use Illuminate\Validation\Rule;

class EntryRequest extends ContentRequest
{
	protected array $uuidFields = ['project_id' => Project::class];

	protected function fields(): array
	{
		return [
			'type' => ['required', Rule::enum(EntryType::class)],
			'project_id' => 'nullable|string|exists:projects,uuid',
			'title' => 'required|string|max:255',
			'description' => 'nullable|string|max:255',
			'year' => 'required|integer|min:1900|max:2100',
			'url' => 'nullable|url|max:255',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return [
			'type' => 'Art',
			'project_id' => 'Projekt',
			'title' => 'Titel',
			'description' => 'Beschreibung',
			'year' => 'Jahr',
			'url' => 'Link',
		];
	}
}
