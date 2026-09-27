<?php

namespace Database\Factories;

use App\Models\GridRow;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class GridRowFactory extends Factory
{
	protected $model = GridRow::class;

	public function definition(): array
	{
		return [
			'gridable_type' => 'project',
			'gridable_id' => Project::factory(),
			'area' => 'main',
			'layout' => '2fr',
			'publish' => true,
			'sort_order' => 0,
		];
	}
}
