<?php

namespace App\Support;

/**
 * Admin URLs for a media file, shared by temp uploads and persisted media.
 */
class MediaUrls
{
	public static function for(string $file, ?string $mimeType, ?array $crop = null, string $directory = 'uploads'): array
	{
		$isImage = str_starts_with((string) $mimeType, 'image/');
		$cropParam = $crop && isset($crop['w'], $crop['h'], $crop['x'], $crop['y'])
			? '&crop=' . $crop['w'] . ',' . $crop['h'] . ',' . $crop['x'] . ',' . $crop['y']
			: '';

		return [
			'original_url' => '/storage/' . $directory . '/' . $file,
			'thumbnail_url' => $isImage ? '/img/' . $directory . '/' . $file . '?w=400&h=400&fit=crop' . $cropParam : null,
			'preview_url' => $isImage ? '/img/' . $directory . '/' . $file . '?w=800&fit=max' . $cropParam : null,
		];
	}
}
