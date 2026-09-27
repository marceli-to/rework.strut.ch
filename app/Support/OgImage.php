<?php

namespace App\Support;

use App\Models\SeoSetting;

class OgImage
{
	public const WIDTH = 1200;
	public const HEIGHT = 630;

	public static function url(?string $file = null, ?SeoSetting $seo = null): string
	{
		$file = $file ?: $seo?->og_image;

		if ($file) {
			if (preg_match('#^(https?:)?//#i', $file) || str_starts_with($file, '/')) {
				return $file;
			}
			return route('og-image', ['file' => $file]);
		}

		return asset('img/forrerzimmermann-opengraph.jpg');
	}
}
