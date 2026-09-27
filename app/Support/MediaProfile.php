<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Resolved media profile (config/media.php).
 */
class MediaProfile
{
	public static function extensions(string $profile): array
	{
		$config = config("media.profiles.$profile") ?? throw new InvalidArgumentException("Unknown media profile [$profile].");

		return collect($config['types'])->flatMap(fn ($type) => config("media.types.$type"))->values()->all();
	}

	/**
	 * All profiles for the admin: extensions, hint and crop ratios.
	 */
	public static function all(): array
	{
		return collect(config('media.profiles'))->map(fn ($config, $key) => [
			'extensions' => array_map(fn ($ext) => '.' . $ext, self::extensions($key)),
			'hint' => collect(self::extensions($key))->reject(fn ($ext) => $ext === 'jpeg')->map('strtoupper')->implode(', ') . ' — max. 200 MB',
			'crops' => collect($config['crops'])->map(fn ($ratio, $label) => [
				'label' => $label,
				'value' => $ratio ? round($ratio[0] / $ratio[1], 6) : null,
			])->values()->all(),
		])->all();
	}
}
