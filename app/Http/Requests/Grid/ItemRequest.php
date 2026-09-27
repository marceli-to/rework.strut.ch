<?php

namespace App\Http\Requests\Grid;

use App\Models\Media;
use App\Models\News;
use Illuminate\Validation\Validator;

class ItemRequest extends GridRequest
{
	public function rules(): array
	{
		return [
			'media_id' => 'required_without:news_id|nullable|string',
			'news_id' => 'required_without:media_id|nullable|string',
		];
	}

	public function after(): array
	{
		return [function (Validator $validator) {
			if ($validator->errors()->isNotEmpty()) {
				return;
			}

			if ($this->filled('media_id') && $this->filled('news_id')) {
				$validator->errors()->add('media_id', 'Entweder ein Bild/Video oder eine News wählen.');
				return;
			}

			$context = $this->context();
			$row = $this->row();
			$position = (int) $this->route('position');

			if (!$context->hasPosition($row->layout, $position)) {
				$validator->errors()->add('position', 'Diese Position gibt es im Layout nicht.');
				return;
			}

			if ($this->filled('media_id') && !$context->mediaQuery($this->owner())->where('uuid', $this->input('media_id'))->exists()) {
				$validator->errors()->add('media_id', 'Dieses Bild kann hier nicht verwendet werden.');
			}

			if ($this->filled('news_id')) {
				if (!$context->acceptsNews($row->layout, $position)) {
					$validator->errors()->add('news_id', 'An dieser Position sind keine News möglich.');
				} elseif (!News::where('uuid', $this->input('news_id'))->exists()) {
					$validator->errors()->add('news_id', 'Diese News gibt es nicht.');
				}
			}
		}];
	}

	/**
	 * Validated uuids resolved to ids.
	 */
	public function payload(): array
	{
		return [
			'media_id' => $this->filled('media_id') ? Media::where('uuid', $this->input('media_id'))->value('id') : null,
			'news_id' => $this->filled('news_id') ? News::where('uuid', $this->input('news_id'))->value('id') : null,
		];
	}

	public function attributes(): array
	{
		return ['media_id' => 'Bild/Video', 'news_id' => 'News'];
	}
}
