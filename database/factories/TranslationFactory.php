<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(3),
            'locale' => fake()->randomElement(['en', 'fr', 'es', 'ar', 'el', 'hr']),
            'value' => fake()->sentence(),
            'tag' => fake()->randomElement(['web', 'mobile', 'desktop']),
        ];
    }
}
