<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum ProjectStatus: string
{
	use HasOptions;

	case Executed = 'executed';
	case Planned = 'planned';
	case Study = 'study';

	public function label(): string
	{
		return match ($this) {
			self::Executed => 'Ausgeführt',
			self::Planned => 'In Planung',
			self::Study => 'Studie',
		};
	}
}
