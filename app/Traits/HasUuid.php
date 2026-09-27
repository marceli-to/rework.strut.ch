<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
	protected static function bootHasUuid(): void
	{
		// Block closure on purpose: `creating` is a halting event, a returned
		// value would stop all later creating listeners (slug, sort order).
		static::creating(function ($model) {
			$model->uuid ??= (string) Str::uuid();
		});
	}

	public function getRouteKeyName(): string
	{
		return 'uuid';
	}
}
