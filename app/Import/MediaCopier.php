<?php

namespace App\Import;

use App\Actions\Media\NormalizeAction;
use App\Actions\Media\UploadAction;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Copies legacy files into storage/app/public/uploads and creates media
 * records. Source files are never moved or changed.
 */
class MediaCopier
{
	public function __construct(
		protected ImportContext $context,
		protected string $source,
	) {}

	public static function sourcePath(string $source, string $file): ?string
	{
		foreach ([$source . '/' . $file, $source . '/downloads/' . $file] as $path) {
			if (is_file($path)) {
				return $path;
			}
		}

		return null;
	}

	/**
	 * @param string $legacyTable, $legacyColumn  identifies the legacy reference (legacy_map)
	 */
	public function attach(Model $owner, string $legacyTable, int $legacyId, string $legacyColumn, ?string $file, string $collection, array $attributes = []): ?Media
	{
		if (!$file) {
			return null;
		}

		$path = self::sourcePath($this->source, $file);
		if (!$path) {
			$this->context->missingFiles[] = "$legacyTable.$legacyColumn #$legacyId: $file";
			return null;
		}

		/** @var Media|null $media */
		$media = $this->context->find($legacyTable, $legacyId, $legacyColumn);
		$disk = Storage::disk('public');

		if (!$media || !$disk->exists('uploads/' . $media->file)) {
			$filename = $media?->file ?? UploadAction::filename(self::originalName($file));
			$mime = File::mimeType($path);

			if (!$this->context->dryRun) {
				$disk->makeDirectory('uploads');
				copy($path, $disk->path('uploads/' . $filename));
				(new NormalizeAction)->execute($disk->path('uploads/' . $filename), $mime);
			}

			[$width, $height] = $this->context->dryRun ? UploadAction::dimensions($path, $mime) : UploadAction::dimensions($disk->path('uploads/' . $filename), $mime);

			$attributes += [
				'file' => $filename,
				'original_name' => self::originalName($file),
				'mime_type' => $mime,
				'size' => $this->context->dryRun ? filesize($path) : filesize($disk->path('uploads/' . $filename)),
				'width' => $width,
				'height' => $height,
			];
		}

		return $this->context->upsert($legacyTable, $legacyId, Media::class, [
			'mediable_type' => $owner->getMorphClass(),
			'mediable_id' => $owner->getKey(),
			'collection' => $collection,
			...$attributes,
		], $legacyColumn);
	}

	/**
	 * "5d8c78036f65b_strut.ch_strutkita02.jpg" → "strutkita02.jpg"
	 */
	public static function originalName(string $file): string
	{
		return preg_replace('/^[0-9a-f]{13}_(strut\.ch_)?/', '', $file);
	}
}
