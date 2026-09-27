<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Support\MediaUrls;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadAction
{
	public function execute(UploadedFile $file): array
	{
		$directory = 'temp';
		$filename = self::filename($file->getClientOriginalName());
		$mimeType = $file->getMimeType();
		$originalName = $file->getClientOriginalName();

		$file->storeAs($directory, $filename, 'public');
		$absolutePath = Storage::disk('public')->path($directory . '/' . $filename);

		(new NormalizeAction)->execute($absolutePath, $mimeType);

		[$width, $height] = self::dimensions($absolutePath, $mimeType);

		return [
			'uuid' => Str::uuid()->toString(),
			'file' => $filename,
			'original_name' => $originalName,
			'mime_type' => $mimeType,
			'size' => @filesize($absolutePath) ?: 0,
			'width' => $width,
			'height' => $height,
			'alt' => null,
			'caption' => null,
			'is_teaser' => false,
			'is_og' => false,
			'variant' => 'desktop',
			'sort_order' => 0,
			'orientation' => Media::orientationFor($width, $height),
			...MediaUrls::for($filename, $mimeType, null, $directory),
			'_temp' => true,
		];
	}

	public static function filename(string $originalName): string
	{
		$name = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
		$extension = Str::lower(pathinfo($originalName, PATHINFO_EXTENSION));

		return $name . '-' . Str::random(6) . '.' . $extension;
	}

	/**
	 * @return array{0: ?int, 1: ?int}
	 */
	public static function dimensions(string $absolutePath, ?string $mimeType): array
	{
		if (!str_starts_with((string) $mimeType, 'image/')) {
			return [null, null];
		}

		$size = @getimagesize($absolutePath);

		return [$size[0] ?? null, $size[1] ?? null];
	}
}
