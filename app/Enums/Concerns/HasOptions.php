<?php

namespace App\Enums\Concerns;

trait HasOptions
{
	/**
	 * Select options for the admin: [['value' => …, 'label' => …], …]
	 */
	public static function options(): array
	{
		return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
	}
}
