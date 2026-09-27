<?php

namespace App\Actions\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadAction
{
	public function execute(UploadedFile $file): array
	{
		$directory = 'temp';
		$filename = $this->uniqueFilename($file->getClientOriginalName());
		$mimeType = $file->getMimeType();
		$originalName = $file->getClientOriginalName();

		$file->storeAs($directory, $filename, 'public');
		$absolutePath = Storage::disk('public')->path($directory . '/' . $filename);

		(new NormalizeAction)->execute($absolutePath, $mimeType);

		$size = @getimagesize($absolutePath);
		$dimensions = [$size[0] ?? null, $size[1] ?? null];

		return [
			'uuid' => Str::uuid()->toString(),
			'file' => $filename,
			'original_name' => $originalName,
			'mime_type' => $mimeType,
			'size' => @filesize($absolutePath) ?: 0,
			'width' => $dimensions[0],
			'height' => $dimensions[1],
			'alt' => null,
			'caption' => null,
			'is_teaser' => false,
			'variant' => 'desktop',
			'sort_order' => 0,
			'orientation' => $this->orientation($dimensions[0], $dimensions[1]),
			'original_url' => '/storage/temp/' . $filename,
			'thumbnail_url' => '/img/temp/' . $filename . '?w=400&h=400&fit=crop',
			'preview_url' => '/img/temp/' . $filename . '?w=800&fit=max',
			'_temp' => true,
		];
	}

	private function uniqueFilename(string $originalName): string
	{
		$name = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
		$extension = Str::lower(pathinfo($originalName, PATHINFO_EXTENSION));
		$suffix = Str::random(6);

		return $name . '-' . $suffix . '.' . $extension;
	}

	private function orientation(?int $width, ?int $height): string
	{
		if (!$width || !$height) {
			return 'unknown';
		}
		if ($width > $height) {
			return 'landscape';
		}
		if ($height > $width) {
			return 'portrait';
		}
		return 'square';
	}
}
