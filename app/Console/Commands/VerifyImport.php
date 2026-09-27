<?php

namespace App\Console\Commands;

use App\Models\GridRow;
use App\Models\LegacyMap;
use App\Models\Media;
use App\Models\Page;
use App\Support\GridContext;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VerifyImport extends Command
{
	protected $signature = 'strut:verify';

	protected $description = 'Check imported data against the legacy database and the file system';

	/** legacy table => 'distinct' for grid elements (one item per row + position) */
	protected const LEGACY_TABLES = [
		'users' => null, 'categories' => null, 'category_types' => null, 'projects' => null,
		'project_images' => null, 'project_videos' => null, 'project_files' => null,
		'project_grids' => null, 'project_grid_elements' => 'distinct',
		'news' => null, 'content' => null, 'content_images' => null,
		'home_grids' => null, 'home_grid_elements' => 'distinct',
		'team' => null, 'jobs' => null, 'books' => null, 'press' => null, 'awards' => null, 'lectures' => null,
	];

	protected array $problems = [];

	public function handle(): int
	{
		$this->counts();
		$this->legacyMap();
		$this->media();
		$this->grids();
		$this->pages();

		$this->newLine();
		if ($this->problems) {
			$this->error(count($this->problems) . ' Problem(e):');
			foreach ($this->problems as $problem) {
				$this->line("  - $problem");
			}
			return self::FAILURE;
		}

		$this->info('Keine Probleme gefunden.');
		return self::SUCCESS;
	}

	protected function counts(): void
	{
		$legacy = DB::connection('legacy');
		$rows = [];

		foreach (self::LEGACY_TABLES as $table => $filter) {
			// grid elements: one per (row, position); legacy duplicates are skipped on import
			$expected = $filter === 'distinct' ? $this->distinctElements($table) : $legacy->table($table)->count();
			$mapped = LegacyMap::where('legacy_table', $table)->where('legacy_column', '')->count();

			// the highlight slideshow is one extra row mapped from home_grids #1
			if ($table === 'home_grids') {
				$mapped += LegacyMap::where('legacy_table', $table)->where('legacy_column', 'highlight')->count();
				$expected++;
			}

			$rows[] = [$table, $expected, $mapped, $expected === $mapped ? '✓' : '≠'];
			if ($expected !== $mapped) {
				$this->problems[] = "$table: $expected legacy, $mapped importiert";
			}
		}

		// single-file columns (media/file) → one media each, if the file existed
		foreach (['news' => ['media'], 'team' => ['media'], 'jobs' => ['media'], 'books' => ['media'], 'press' => ['media', 'file'], 'awards' => ['media', 'file'], 'lectures' => ['media', 'file']] as $table => $columns) {
			foreach ($columns as $column) {
				$expected = $legacy->table($table)->whereNotNull($column)->where($column, '<>', '')->count();
				$mapped = LegacyMap::where('legacy_table', $table)->where('legacy_column', $column)->count();
				$rows[] = ["$table.$column", $expected, $mapped, $expected === $mapped ? '✓' : '≠ (fehlende Datei?)'];
			}
		}

		$this->table(['Legacy', 'Erwartet', 'Importiert', ''], $rows);
	}

	/**
	 * Grid elements expected on import: one per (row, position) – except the
	 * legacy highlight slideshow, whose slides all share position 0.
	 */
	protected function distinctElements(string $table): int
	{
		$query = fn () => DB::connection('legacy')->table($table)
			->when($table === 'home_grid_elements', fn ($q) => $q->where('environment', 'production'));
		$highlight = \App\Import\LegacyImport::HIGHLIGHT_GRID_ID;

		if ($table !== 'home_grid_elements') {
			return $query()->distinct()->count(DB::raw('CONCAT(grid_id, "-", position)'));
		}

		return $query()->where('grid_id', '<>', $highlight)->distinct()->count(DB::raw('CONCAT(grid_id, "-", position)'))
			+ $query()->where('grid_id', $highlight)->count();
	}

	protected function legacyMap(): void
	{
		$dangling = LegacyMap::all()->filter(function (LegacyMap $map) {
			$class = Relation::getMorphedModel($map->model_type) ?? $map->model_type;
			return !$class::whereKey($map->model_id)->exists();
		});

		$this->line('legacy_map ohne Datensatz: ' . $dangling->count());
		foreach ($dangling as $map) {
			$this->problems[] = "legacy_map {$map->legacy_table}#{$map->legacy_id} → {$map->model_type}#{$map->model_id} fehlt";
		}
	}

	protected function media(): void
	{
		$disk = Storage::disk('public');
		$media = Media::with('mediable')->get();

		$orphans = $media->filter(fn (Media $m) => !$m->mediable);
		$missing = $media->reject(fn (Media $m) => $disk->exists('uploads/' . $m->file));
		$unreferenced = collect($disk->files('uploads'))->map(fn ($path) => basename($path))->diff($media->pluck('file'));

		$this->line("Medien: {$media->count()}, ohne Besitzer: {$orphans->count()}, Datei fehlt: {$missing->count()}, Dateien ohne Datensatz: {$unreferenced->count()}");

		foreach ($orphans as $m) {
			$this->problems[] = "Media {$m->uuid} ({$m->original_name}) hat keinen Besitzer";
		}
		foreach ($missing as $m) {
			$this->problems[] = "Media {$m->uuid}: Datei uploads/{$m->file} fehlt";
		}
		foreach ($unreferenced as $file) {
			$this->problems[] = "uploads/$file hat keinen Media-Datensatz";
		}
	}

	protected function grids(): void
	{
		$rows = GridRow::with(['gridable', 'items.media'])->get();
		$items = 0;

		foreach ($rows as $row) {
			if (!$row->gridable) {
				$this->problems[] = "Rasterzeile {$row->uuid} ohne Besitzer";
				continue;
			}

			$context = GridContext::for($row->gridable->gridContext());
			$allowed = $context->mediaQuery($row->gridable)->pluck('id')->flip();

			if (!$context->allows($row->area, $row->layout)) {
				$this->problems[] = "Rasterzeile {$row->uuid}: Layout {$row->layout} in {$context->key}/{$row->area} nicht erlaubt";
			}

			foreach ($row->items as $item) {
				$items++;
				$where = "{$context->key} Zeile {$row->uuid} Position {$item->position}";

				if (!$context->hasPosition($row->layout, $item->position)) {
					$this->problems[] = "$where: Position ausserhalb des Layouts {$row->layout}";
				}
				if ($item->news_id && !$context->acceptsNews($row->layout, $item->position)) {
					$this->problems[] = "$where: News an dieser Position nicht erlaubt";
				}
				if ($item->media_id && !$allowed->has($item->media_id)) {
					$this->problems[] = "$where: Bild gehört nicht zum erlaubten Bereich (z.B. unveröffentlichtes Projekt)";
				}
			}
		}

		$this->line("Raster: {$rows->count()} Zeilen, $items Elemente geprüft");
	}

	protected function pages(): void
	{
		$missing = array_diff(array_keys(Page::KEYS), Page::pluck('key')->all());

		foreach ($missing as $key) {
			$this->problems[] = "Seite '$key' fehlt";
		}
	}
}
