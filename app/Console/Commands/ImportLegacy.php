<?php

namespace App\Console\Commands;

use App\Import\ImportContext;
use App\Import\LegacyImport;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ImportLegacy extends Command
{
	protected $signature = 'strut:import
		{--fresh : Delete all imported content (and its files) before importing}
		{--dry-run : Run everything in a transaction that is rolled back; no files are copied}';

	protected $description = 'Import content and media from the legacy strut.ch database (idempotent)';

	/** Content tables, children first. Users are never wiped. */
	protected const TABLES = ['grid_items', 'grid_rows', 'media', 'entries', 'projects', 'category_types', 'categories', 'news', 'pages', 'team_members', 'job_listings', 'books', 'legacy_map'];

	public function handle(): int
	{
		$source = config('strut.legacy_media_path');
		if (!$source || !is_dir($source)) {
			$this->error('LEGACY_MEDIA_PATH is not set or not a directory.');
			return self::FAILURE;
		}

		$dryRun = (bool) $this->option('dry-run');
		$context = new ImportContext($dryRun);

		if ($dryRun) {
			$this->warn('Dry run: database changes are rolled back, no files are copied.');
		}

		$started = microtime(true);

		try {
			DB::transaction(function () use ($context, $source, $dryRun) {
				if ($this->option('fresh')) {
					$this->wipe($dryRun);
				}

				(new LegacyImport($context, $source))->run();

				if ($dryRun) {
					throw new RuntimeException('dry-run');
				}
			});
		} catch (RuntimeException $e) {
			if ($e->getMessage() !== 'dry-run') {
				throw $e;
			}
		}

		$this->report($context, microtime(true) - $started);

		return self::SUCCESS;
	}

	protected function wipe(bool $dryRun): void
	{
		$this->warn('--fresh: removing imported content and uploaded files.');

		if (!$dryRun) {
			foreach (Media::pluck('file') as $file) {
				Storage::disk('public')->delete('uploads/' . $file);
			}
		}

		Schema::disableForeignKeyConstraints();
		foreach (self::TABLES as $table) {
			DB::table($table)->delete();
		}
		Schema::enableForeignKeyConstraints();
	}

	protected function report(ImportContext $context, float $seconds): void
	{
		$this->newLine();
		$this->table(['Inhalt', 'Legacy', 'Importiert', ''], collect($context->counts)->map(fn ($c, $label) => [
			$label, $c['legacy'], $c['imported'], $c['legacy'] === $c['imported'] ? '✓' : '≠',
		])->values());

		$this->line('Übersprungen: ' . count($context->skipped));
		foreach ($context->skipped as [$label, $id, $reason]) {
			$this->line("  - $label #$id: $reason");
		}

		$this->line('Fehlende Dateien: ' . count($context->missingFiles));
		foreach ($context->missingFiles as $file) {
			$this->line("  - $file");
		}

		$this->line('Nicht referenzierte Dateien (nicht importiert): ' . count($context->unusedFiles));
		foreach ($context->unusedFiles as $file) {
			$this->line("  - $file");
		}

		$this->line('ß → ss ersetzt: ' . $context->clean->eszett);
		$this->info(sprintf('Fertig in %.1f s%s.', $seconds, $context->dryRun ? ' (dry run, nichts gespeichert)' : ''));
	}
}
