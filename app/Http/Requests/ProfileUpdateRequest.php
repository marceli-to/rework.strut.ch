<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name ist erforderlich',
            'name.max' => 'Name darf maximal 255 Zeichen lang sein',
            'email.required' => 'E-Mail ist erforderlich',
            'email.email' => 'Bitte eine gültige E-Mail-Adresse eingeben',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet',
        ];
    }
}
