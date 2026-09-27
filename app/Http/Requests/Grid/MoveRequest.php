<?php

namespace App\Http\Requests\Grid;

use Illuminate\Validation\Validator;

class MoveRequest extends GridRequest
{
	public function rules(): array
	{
		return [
			'from_row' => 'required|string',
			'from_position' => 'required|integer|min:0',
			'to_row' => 'required|string',
			'to_position' => 'required|integer|min:0',
		];
	}

	public function after(): array
	{
		return [function (Validator $validator) {
			if ($validator->errors()->isNotEmpty()) {
				return;
			}

			$context = $this->context();
			$from = $this->row($this->input('from_row'));
			$to = $this->row($this->input('to_row'));
			$item = $from->items()->where('position', $this->input('from_position'))->first();
			$target = $to->items()->where('position', $this->input('to_position'))->first();

			if (!$item) {
				$validator->errors()->add('from_position', 'An dieser Position ist nichts platziert.');
				return;
			}

			if ($from->area !== $to->area || !$context->hasPosition($to->layout, $this->input('to_position'))) {
				$validator->errors()->add('to_position', 'Diese Position ist nicht möglich.');
				return;
			}

			// news may only land (or be swapped back) in news-capable cells
			if (($item->news_id && !$context->acceptsNews($to->layout, $this->input('to_position')))
				|| ($target?->news_id && !$context->acceptsNews($from->layout, $this->input('from_position')))) {
				$validator->errors()->add('to_position', 'News sind an dieser Position nicht möglich.');
			}
		}];
	}
}
