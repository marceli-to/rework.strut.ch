<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTeamRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'items' => 'required|array',
			'items.*.uuid' => 'required|string|exists:team_members,uuid',
			'items.*.sort_order' => 'required|integer',
		];
	}
}
