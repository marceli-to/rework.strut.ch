<?php

namespace App\Http\Requests\Seo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'landing_meta_description' => 'nullable|string|max:500',
            'projects_meta_description' => 'nullable|string|max:500',
            'werkliste_meta_description' => 'nullable|string|max:500',
            'profile_meta_description' => 'nullable|string|max:500',
            'team_meta_description' => 'nullable|string|max:500',
            'jobs_meta_description' => 'nullable|string|max:500',
            'contact_meta_description' => 'nullable|string|max:500',
            'media' => 'nullable|array',
            'media.*.uuid' => 'required|string',
            'media.*.file' => 'required|string',
            'media.*.original_name' => 'required|string',
            'media.*.mime_type' => 'required|string',
            'media.*.size' => 'required|integer',
            'media.*.width' => 'nullable|integer',
            'media.*.height' => 'nullable|integer',
            'media.*.alt' => 'nullable|string|max:255',
            'media.*.caption' => 'nullable|string|max:255',
            'media.*.is_og' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'landing_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
            'projects_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
            'werkliste_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
            'profile_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
            'team_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
            'jobs_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
            'contact_meta_description.max' => 'Meta Description darf maximal 500 Zeichen lang sein',
        ];
    }
}
