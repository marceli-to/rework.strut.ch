<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Splits keyed groups (e.g. years) into columns of whole groups, as the legacy
 * AppHelper::partition: each column holds ceil(groups / columns) groups.
 */
class Columns
{
	public static function split(Collection $groups, int $columns = 3): Collection
	{
		if ($groups->isEmpty()) {
			return collect();
		}

		return $groups->chunk((int) ceil($groups->count() / $columns))->values();
	}
}
