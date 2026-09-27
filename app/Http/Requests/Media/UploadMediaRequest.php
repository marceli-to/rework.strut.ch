<?php

namespace App\Http\Requests\Media;

use App\Support\MediaProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadMediaRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		$profile = $this->input('profile', 'project');
		$known = array_key_exists($profile, config('media.profiles'));

		return [
			'profile' => ['sometimes', Rule::in(array_keys(config('media.profiles')))],
			'file' => ['required', 'file', 'max:204800', ...($known ? ['mimes:' . implode(',', MediaProfile::extensions($profile))] : [])],
		];
	}

	public function messages(): array
	{
		$profile = $this->input('profile', 'project');
		$allowed = array_key_exists($profile, config('media.profiles'))
			? collect(MediaProfile::extensions($profile))->map('strtoupper')->implode(', ')
			: '';

		return [
			'file.required' => 'Bitte eine Datei auswählen',
			'file.mimes' => "Hier sind nur diese Dateitypen erlaubt: $allowed",
			'file.max' => 'Die Datei darf maximal 200 MB gross sein',
		];
	}
}
