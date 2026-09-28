<?php

namespace App\Actions\Site;

use App\Enums\EntryType;
use App\Models\Entry;
use App\Support\Columns;
use Illuminate\Support\Collection;

/**
 * Published press, award or lecture entries, grouped by year (newest first)
 * and split into three columns of whole years.
 */
class GetEntries
{
	/**
	 * @return Collection<int, Collection<int, Collection<int, Entry>>> columns → year → entries
	 */
	public function execute(EntryType $type): Collection
	{
		return Columns::split(
			Entry::ofType($type)
				->published()
				->ordered()
				->with(['images', 'files', 'project'])
				->get()
				->groupBy('year')
		);
	}
}
