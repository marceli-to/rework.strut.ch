<?php

namespace App\Actions\Media;

class NormalizeAction
{
	public const MAX_SOURCE_EDGE = 3200;
	public const NORMALIZABLE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

	/**
	 * Downsize a file in place if its longest edge exceeds MAX_SOURCE_EDGE.
	 *
	 * Returns the scale factor applied (1.0 if untouched), or null on failure.
	 */
	public function execute(string $absolutePath, string $mimeType): ?float
	{
		if (!in_array($mimeType, self::NORMALIZABLE_MIMES, true)) {
			return 1.0;
		}

		if (!is_file($absolutePath)) {
			return null;
		}

		$size = @getimagesize($absolutePath);
		if (!$size || !$size[0] || !$size[1]) {
			return null;
		}

		[$width, $height] = $size;
		$longest = max($width, $height);
		if ($longest <= self::MAX_SOURCE_EDGE) {
			return 1.0;
		}

		$scale = self::MAX_SOURCE_EDGE / $longest;

		try {
			$image = new \Imagick($absolutePath);

			$profiles = $image->getImageProfiles('icc', true);
			$image->stripImage();
			if (!empty($profiles['icc'])) {
				$image->profileImage('icc', $profiles['icc']);
			}

			if ($width >= $height) {
				$image->thumbnailImage(self::MAX_SOURCE_EDGE, 0);
			} else {
				$image->thumbnailImage(0, self::MAX_SOURCE_EDGE);
			}

			if ($mimeType === 'image/jpeg') {
				$image->setImageCompressionQuality(90);
				$image->setInterlaceScheme(\Imagick::INTERLACE_JPEG);
			} elseif ($mimeType === 'image/webp') {
				$image->setImageCompressionQuality(90);
			}

			$image->writeImage($absolutePath);
			$image->clear();
			clearstatcache(true, $absolutePath);
		} catch (\Throwable $e) {
			report($e);
			return null;
		}

		return $scale;
	}
}
