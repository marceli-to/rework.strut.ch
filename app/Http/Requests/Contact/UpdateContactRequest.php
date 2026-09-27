<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'name' => 'required|string|max:255',
			'address' => 'required|string|max:255',
			'email' => 'required|string|email',
			'phone' => 'required|string|max:255',
			'maps_url' => 'nullable|string|url',
			'imprint' => 'nullable|string',
			'publish' => 'boolean',
		];
	}

	public function messages(): array
	{
		return [
			'name.required' => 'Name ist erforderlich',
			'name.max' => 'Name darf maximal 255 Zeichen lang sein',
			'address.required' => 'Adresse ist erforderlich',
			'address.max' => 'Adresse darf maximal 255 Zeichen lang sein',
			'email.required' => 'E-Mail ist erforderlich',
			'email.email' => 'Bitte eine gültige E-Mail-Adresse eingeben',
			'phone.required' => 'Telefon ist erforderlich',
			'phone.max' => 'Telefon darf maximal 255 Zeichen lang sein',
			'maps_url.url' => 'Bitte eine gültige URL eingeben',
		];
	}
}
