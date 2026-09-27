<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class CropMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'x' => 'nullable|integer|min:0|required_with:y,w,h',
            'y' => 'nullable|integer|min:0|required_with:x,w,h',
            'w' => 'nullable|integer|min:1|required_with:x,y,h',
            'h' => 'nullable|integer|min:1|required_with:x,y,w',
        ];
    }
}
