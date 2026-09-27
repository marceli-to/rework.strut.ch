<?php

namespace App\Import;

use App\Models\LegacyMap;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared state of one import run: legacy id mapping and the report.
 */
class ImportContext
{
	public array $counts = [];   // [label => ['legacy' => n, 'imported' => n]]
	public array $skipped = [];  // [[label, legacy id, reason]]
	public array $missingFiles = [];
	public array $unusedFiles = [];

	public function __construct(
		public readonly bool $dryRun,
		public readonly HtmlCleaner $clean = new HtmlCleaner,
	) {}

	public function find(string $table, int $id, string $column = ''): ?Model
	{
		$map = LegacyMap::where(['legacy_table' => $table, 'legacy_id' => $id, 'legacy_column' => $column])->first();

		return $map ? (new ($this->modelClass($map->model_type)))::find($map->model_id) : null;
	}

	public function id(string $table, ?int $id, string $column = ''): ?int
	{
		if (!$id) {
			return null;
		}

		return LegacyMap::where(['legacy_table' => $table, 'legacy_id' => $id, 'legacy_column' => $column])->value('model_id');
	}

	/**
	 * Create or update the model mapped to a legacy row (idempotent).
	 *
	 * @param class-string<Model> $model
	 */
	public function upsert(string $table, int $id, string $model, array $attributes, string $column = ''): Model
	{
		$record = $this->find($table, $id, $column);

		if ($record) {
			$record->fill($attributes)->save();
		} else {
			$record = new $model;
			$record->forceFill($attributes)->save();
			$this->remember($table, $id, $record, $column);
		}

		return $record;
	}

	public function remember(string $table, int $id, Model $model, string $column = ''): void
	{
		LegacyMap::updateOrCreate(
			['legacy_table' => $table, 'legacy_id' => $id, 'legacy_column' => $column],
			['model_type' => $model->getMorphClass(), 'model_id' => $model->getKey()],
		);
	}

	public function count(string $label, int $legacy, int $imported): void
	{
		$this->counts[$label] = ['legacy' => $legacy, 'imported' => $imported];
	}

	public function skip(string $label, int|string $id, string $reason): void
	{
		$this->skipped[] = [$label, $id, $reason];
	}

	protected function modelClass(string $morph): string
	{
		return \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($morph) ?? $morph;
	}
}
