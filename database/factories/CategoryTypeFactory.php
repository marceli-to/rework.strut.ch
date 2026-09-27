<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTypeFactory extends Factory
{
	protected $model = CategoryType::class;

	public function definition(): array
	{
		$name = fake()->unique()->word();

		return [
			'category_id' => Category::factory(),
			'name_singular' => ucfirst($name),
			'name_plural' => ucfirst($name) . 'en',
			'publish' => true,
		];
	}
}
