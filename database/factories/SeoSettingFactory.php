<?php

namespace Database\Factories;

use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeoSettingFactory extends Factory
{
    protected $model = SeoSetting::class;

    public function definition(): array
    {
        return [
            'landing_meta_description' => fake()->sentence(),
            'projects_meta_description' => fake()->sentence(),
            'werkliste_meta_description' => fake()->sentence(),
            'profile_meta_description' => fake()->sentence(),
            'team_meta_description' => fake()->sentence(),
            'jobs_meta_description' => fake()->sentence(),
            'contact_meta_description' => fake()->sentence(),
        ];
    }
}
