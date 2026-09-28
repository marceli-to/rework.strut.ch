<?php

namespace App\Console\Commands;

use App\Models\LegacyMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time backfill of legacy_map.legacy_file for media imported before the
 * import recorded it. Reads the legacy database (read-only).
 */
class BackfillLegacyFiles extends Command
{
	protected $signature = 'strut:legacy-files';

	protected $description = 'Fill in the legacy file names of imported media (for old media URL redirects)';

	/**
	 * Legacy table → column holding the file name when legacy_column is empty.
	 */
	private const NAME_COLUMNS = [
		'project_images' => 'name',
		'project_files' => 'name',
		'content_images' => 'name',
	];

	public function handle(): int
	{
		$legacy = DB::connection('legacy');
		$rows = LegacyMap::where('model_type', 'media')->whereNull('legacy_file')->get();
		$filled = 0;

		foreach ($rows as $row) {
			$file = match (true) {
				$row->legacy_table === 'orphan_files' => $row->legacy_column,
				isset(self::NAME_COLUMNS[$row->legacy_table]) => $legacy->table($row->legacy_table)->where('id', $row->legacy_id)->value(self::NAME_COLUMNS[$row->legacy_table]),
				$row->legacy_column !== '' => $legacy->table($row->legacy_table)->where('id', $row->legacy_id)->value($row->legacy_column),
				default => null,
			};

			if ($file) {
				$row->update(['legacy_file' => $file]);
				$filled++;
			} else {
				$this->warn("No file for {$row->legacy_table} #{$row->legacy_id} ({$row->legacy_column})");
			}
		}

		$this->info("{$filled} of {$rows->count()} media mapped.");

		return self::SUCCESS;
	}
}
