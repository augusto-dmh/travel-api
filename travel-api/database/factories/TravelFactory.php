<?php

namespace Database\Factories;

use App\Models\Travel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Travel>
 */
class TravelFactory extends Factory
{
    protected $model = Travel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'is_public' => fake()->boolean(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->text(),
            'number_of_days' => fake()->numberBetween(1, 30),
        ];
    }
}
