<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasSortOrder
{
	/**
	 * New records are appended at the end (the template left them at 0).
	 */
	protected static function bootHasSortOrder(): void
	{
		static::creating(function ($model) {
			if ($model->sort_order === null) {
				$query = method_exists($model, 'sortGroup') ? $model->sortGroup(static::query()) : static::query();
				$model->sort_order = ($query->max('sort_order') ?? -1) + 1;
			}
		});
	}

	public function scopeOrdered(Builder $query): Builder
	{
		return $query->orderBy('sort_order')->orderBy('id');
	}
}
