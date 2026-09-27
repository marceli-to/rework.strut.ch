<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'variant' => 'sometimes|in:desktop,mobile',
			'alt' => 'nullable|string|max:255',
			'caption' => 'nullable|string|max:255',
		];
	}
}
