<?php

namespace App\Http\Requests\Grid;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RowRequest extends GridRequest
{
	public function rules(): array
	{
		$creating = $this->isMethod('post');

		return [
			'area' => [$creating ? 'required' : 'prohibited', Rule::in($this->context()->areas())],
			'layout' => [$creating ? 'required' : 'sometimes', 'string'],
			'publish' => 'sometimes|boolean',
		];
	}

	public function after(): array
	{
		return [function (Validator $validator) {
			if ($validator->errors()->isNotEmpty()) {
				return;
			}

			$area = $this->input('area') ?? $this->row()->area;
			$context = $this->context();

			if ($this->has('layout') && !$context->allows($area, $this->input('layout'))) {
				$validator->errors()->add('layout', 'Dieses Layout ist hier nicht erlaubt.');
			}

			$max = $context->area($area)['max_rows'] ?? null;
			if ($this->isMethod('post') && $max && $this->owner()->gridRows()->where('area', $area)->count() >= $max) {
				$validator->errors()->add('area', 'In diesem Bereich ist nur eine Zeile möglich.');
			}
		}];
	}

	public function attributes(): array
	{
		return ['area' => 'Bereich', 'layout' => 'Layout'];
	}
}
