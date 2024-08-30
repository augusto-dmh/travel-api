<?php

namespace Database\Factories;

use App\Models\Travel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $travel = Travel::factory()->create();
        $startingDate = fake()->date();
        $endingDate = Carbon::parse($startingDate)->addDays($travel->number_of_days)->format('Y-m-d');

        return [
            'travel_id' => $travel->id,
            'name' => fake()->name(),
            'starting_date' => $startingDate,
            'ending_date' => $endingDate,
            'price' => fake()->numberBetween(1000, 9999),
        ];
    }
}
