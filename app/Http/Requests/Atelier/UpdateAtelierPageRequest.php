<?php

namespace App\Http\Requests\Atelier;

use App\Http\Requests\Traits\HasMediaRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAtelierPageRequest extends FormRequest
{
	use HasMediaRules;
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'sometimes|required|string|max:255',
			'text' => 'sometimes|required|string',
			'publish' => 'boolean',
			...$this->mediaRules(),
		];
	}

	public function messages(): array
	{
		return [
			'title.required' => 'Titel ist erforderlich',
			'title.max' => 'Titel darf maximal 255 Zeichen lang sein',
			'text.required' => 'Text ist erforderlich',
		];
	}
}
