<?php

namespace Database\Factories;

use App\Models\GridItem;
use App\Models\GridRow;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class GridItemFactory extends Factory
{
	protected $model = GridItem::class;

	public function definition(): array
	{
		return [
			'grid_row_id' => GridRow::factory(),
			'position' => 0,
			'media_id' => Media::factory(),
			'news_id' => null,
		];
	}
}
