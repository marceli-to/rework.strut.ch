<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum Competition: string
{
	use HasOptions;

	case FirstPrize = 'first_prize';
	case SecondPrize = 'second_prize';
	case Other = 'other';

	public function label(): string
	{
		return match ($this) {
			self::FirstPrize => '1. Preis',
			self::SecondPrize => '2. Preis',
			self::Other => 'Andere',
		};
	}
}
