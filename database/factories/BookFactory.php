<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
	protected $model = Book::class;

	public function definition(): array
	{
		return [
			'title' => fake()->words(3, true),
			'description' => '104 Seiten, 23.0 x 30.5 cm',
			'info' => '<p>' . fake()->paragraph() . '</p>',
			'url' => fake()->url(),
			'publish' => false,
		];
	}
}
