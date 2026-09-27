<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'firstname' => 'required|string|max:255',
			'name' => 'required|string|max:255',
			'title' => 'nullable|string|max:255',
			'email' => 'nullable|string|email',
			'cv' => 'nullable|string',
			'publish' => 'boolean',
			'former' => 'boolean',
		];
	}

	public function messages(): array
	{
		return [
			'firstname.required' => 'Vorname ist erforderlich',
			'firstname.max' => 'Vorname darf maximal 255 Zeichen lang sein',
			'name.required' => 'Name ist erforderlich',
			'name.max' => 'Name darf maximal 255 Zeichen lang sein',
			'title.max' => 'Titel darf maximal 255 Zeichen lang sein',
			'email.email' => 'Bitte eine gültige E-Mail-Adresse eingeben',
		];
	}
}
