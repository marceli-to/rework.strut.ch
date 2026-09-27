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
			'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,pdf|max:204800',
		];
	}

	public function messages(): array
	{
		return [
			'file.required' => 'Bitte eine Datei auswählen',
			'file.mimes' => 'Nur JPG, PNG, WebP, GIF, MP4, WebM, MOV und PDF Dateien sind erlaubt',
			'file.max' => 'Die Datei darf maximal 200 MB gross sein',
		];
	}
}
