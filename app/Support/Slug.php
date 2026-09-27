<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slug
{
	/**
	 * Same algorithm as the legacy AppHelper::getSlug(), so project URLs
	 * (/bauten/{id}/{name-location-year}) stay identical.
	 */
	public static function project(?string $name, ?string $location, int|string|null $year): string
	{
		return Str::slug(self::transliterate($name) . '-' . self::transliterate($location) . '-' . $year);
	}

	public static function transliterate(?string $value): string
	{
		return str_replace(
			['ä', 'ö', 'ü', 'é', 'è', 'â', 'à', 'ç'],
			['ae', 'oe', 'ue', 'e', 'e', 'a', 'a', 'c'],
			mb_strtolower((string) $value, 'UTF-8'),
		);
	}

	/**
	 * @param class-string<Model> $model
	 */
	public static function unique(string $slug, string $model, ?int $ignoreId = null): string
	{
		$slug = Str::slug($slug);
		$candidate = $slug;
		$i = 2;

		while ($model::where('slug', $candidate)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
			$candidate = $slug . '-' . $i++;
		}

		return $candidate;
	}
}
