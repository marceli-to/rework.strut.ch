<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Uploads land in storage/app/public/temp and are moved to uploads/ when the
 * form is saved. Files left behind (form never saved) are removed here,
 * together with their cached admin thumbnails. Scheduled daily.
 */
class CleanTempMedia extends Command
{
	protected $signature = 'media:clean-temp
		{--hours=24 : Only delete files older than this}
		{--dry-run : List the files without deleting them}';

	protected $description = 'Delete abandoned temporary uploads';

	public function handle(): int
	{
		$disk = Storage::disk('public');
		$threshold = now()->subHours((int) $this->option('hours'))->getTimestamp();

		$files = collect($disk->files('temp'))
			->filter(fn (string $path) => $disk->lastModified($path) < $threshold);

		foreach ($files as $path) {
			$this->line(($this->option('dry-run') ? 'would delete ' : 'deleted ') . $path);

			if (!$this->option('dry-run')) {
				$disk->delete($path);
				// Glide caches /img/temp/{file} under .glide-cache/temp/{file}/
				File::deleteDirectory(storage_path('app/.glide-cache/' . $path));
			}
		}

		$this->info($files->count() . ' temporäre Datei(en) ' . ($this->option('dry-run') ? 'gefunden' : 'gelöscht') . '.');

		return self::SUCCESS;
	}
}
