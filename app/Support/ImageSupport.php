<?php

namespace App\Support;

use Imagick;

class ImageSupport
{
	protected static ?array $supportedFormats = null;

	public static function supportedFormats(): array
	{
		if (self::$supportedFormats !== null) {
			return self::$supportedFormats;
		}

		$formats = ['jpg', 'jpeg', 'png', 'gif'];

		if (class_exists(Imagick::class)) {
			if (Imagick::queryFormats('WEBP')) {
				$formats[] = 'webp';
			}
			if (Imagick::queryFormats('AVIF')) {
				$formats[] = 'avif';
			}
		}

		return self::$supportedFormats = $formats;
	}

	public static function supports(string $format): bool
	{
		return in_array(strtolower($format), self::supportedFormats(), true);
	}
}
