<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EntryType: string
{
	use HasOptions;

	case Press = 'press';
	case Award = 'award';
	case Lecture = 'lecture';

	public function label(): string
	{
		return match ($this) {
			self::Press => 'Presse',
			self::Award => 'Auszeichnungen',
			self::Lecture => 'Vorträge',
		};
	}
}
