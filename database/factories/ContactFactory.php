<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'maps_url' => 'https://maps.google.com/?q=' . urlencode(fake()->address()),
            'imprint' => fake()->paragraph(),
            'publish' => true,
        ];
    }
}
