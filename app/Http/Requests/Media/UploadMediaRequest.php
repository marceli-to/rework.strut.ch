<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif|max:51200',
		];
	}

	public function messages(): array
	{
		return [
			'file.required' => 'Bitte eine Datei auswählen',
			'file.mimes' => 'Nur JPG, PNG, WebP und GIF Dateien sind erlaubt',
			'file.max' => 'Die Datei darf maximal 50 MB gross sein',
		];
	}
}
