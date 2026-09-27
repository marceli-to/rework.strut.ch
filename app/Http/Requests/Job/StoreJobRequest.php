<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string|max:255',
			'text' => 'required|string',
			'publish' => 'boolean',
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
