<?php

namespace App\Traits;

use App\Models\GridRow;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasGrid
{
	protected static function bootHasGrid(): void
	{
		// Block closure: model events halt on a returned value.
		static::deleting(function ($model) {
			$model->gridRows()->get()->each->delete();
		});
	}

	public function gridRows(): MorphMany
	{
		return $this->morphMany(GridRow::class, 'gridable')->orderBy('sort_order')->orderBy('id');
	}

	/**
	 * Grid context key in config/grids.php.
	 */
	public function gridContext(): string
	{
		return $this->getMorphClass();
	}
}
