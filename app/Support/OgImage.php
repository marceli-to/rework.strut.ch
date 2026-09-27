<?php

namespace App\Support;

class OgImage
{
	public const WIDTH = 1200;
	public const HEIGHT = 630;

	public static function url(?string $file = null): string
	{
		if ($file) {
			if (preg_match('#^(https?:)?//#i', $file) || str_starts_with($file, '/')) {
				return $file;
			}
			return route('og-image', ['file' => $file]);
		}

		return asset('img/strut-og.png');
	}
}
