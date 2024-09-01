<?php

namespace Tests\Unit;

use App\Http\Resources\TourResource;
use Tests\TestCase;
use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TourTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $tour = Tour::factory()->create();

        // to get the tour with the price already formatted by the trigger (999 -> 99900)
        $tour->refresh();

        $this->assertDatabaseHas('tours', [
            'travel_id' => $tour->travel_id,
            'name' => $tour->name,
            'starting_date' => $tour->starting_date,
            'ending_date' => $tour->ending_date,
            'price' => $tour->price,
        ]);
    }

    public function test_price_is_formatted(): void
    {
        $tour = Tour::factory()->create(['price' => 999]);

        $this->assertDatabaseHas('tours', [
            'price' => $tour->price * 100,
        ]);
    }

    public function test_dates_inconsistent_with_number_of_days_of_the_travel_throws_query_exception(): void
    {
        $this->expectException(QueryException::class);

        $travel = Travel::factory()->create(['number_of_days' => 5]);

        $tour = Tour::factory()->create(['travel_id' => $travel->id, 'starting_date' => '2015-05-05', 'ending_date' => '2015-05-25']);

        $this->assertDatabaseHas('tours', [
            'price' => $tour->price * 100,
        ]);
    }

    public function test_paginated_tours_can_be_got_by_travel_slug(): void {
        $travel = Travel::factory()->create();
        $tours = Tour::factory(10)->create([
            'travel_id' => $travel->id,
            // there's a trigger that ensures the difference between both dates is consistent with 'number_of_days' from the associated travel.
            'starting_date' => now()->startOfDay()->format('Y-m-d'),
            'ending_date' => now()->startOfDay()->addDays($travel->number_of_days)->format('Y-m-d'),
        ]);
        $tours->each->refresh();
        $expectedResponseData = TourResource::collection($tours)->resolve();

        $response = $this->get(route('tour.index', ['travel_slug' => $travel->slug]));

        $response->assertStatus(200);
        $response->assertJsonPath('meta.total', count($tours));
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJson(['data' => $expectedResponseData]);
    }
}
