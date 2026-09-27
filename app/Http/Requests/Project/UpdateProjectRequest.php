<?php

namespace App\Http\Requests\Project;

use App\Http\Requests\Traits\HasMediaRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
	use HasMediaRules;
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string|max:255',
			'location' => 'required|string|max:255',
			'slug' => 'nullable|string|max:255',
			'subtitle' => 'nullable|string|max:255',
			'year' => 'required|integer',
			'description' => 'nullable|string',
			'info' => 'nullable|string',
			'meta_description' => 'nullable|string|max:255',
			'publish' => 'boolean',
			'feature' => 'boolean',
			'detail' => 'boolean',
			'topic_id' => 'nullable|string|exists:topics,uuid',
			...$this->mediaRules(),
		];
	}

	public function messages(): array
	{
		return [
			'title.required' => 'Titel ist erforderlich',
			'title.max' => 'Titel darf maximal 255 Zeichen lang sein',
			'year.required' => 'Jahr ist erforderlich',
			'year.integer' => 'Jahr muss eine Zahl sein',
			'location.required' => 'Ort ist erforderlich',
			'location.max' => 'Ort darf maximal 255 Zeichen lang sein',
			'slug.max' => 'Slug darf maximal 255 Zeichen lang sein',
			'subtitle.max' => 'Untertitel darf maximal 255 Zeichen lang sein',
			'meta_description.max' => 'Meta Description darf maximal 255 Zeichen lang sein',
			'topic_id.exists' => 'Das gewählte Thema ist ungültig',
		];
	}
}
