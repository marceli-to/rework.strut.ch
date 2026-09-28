<?php

namespace App\Actions\Site;

use App\Enums\EntryType;
use App\Models\Entry;
use Illuminate\Support\Collection;

/**
 * Published press, award or lecture entries, grouped by year (newest first)
 * and split into columns of whole years, as the legacy AppHelper::partition:
 * each column holds ceil(years / 3) years.
 */
class GetEntries
{
	public const COLUMNS = 3;

	/**
	 * @return Collection<int, Collection<int, Collection<int, Entry>>> columns → year → entries
	 */
	public function execute(EntryType $type): Collection
	{
		$years = Entry::ofType($type)
			->published()
			->ordered()
			->with(['images', 'files', 'project'])
			->get()
			->groupBy('year');

		if ($years->isEmpty()) {
			return collect();
		}

		return $years->chunk((int) ceil($years->count() / self::COLUMNS))->values();
	}
}
