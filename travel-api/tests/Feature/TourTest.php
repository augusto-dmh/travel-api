<?php

namespace Tests\Unit;

use App\Http\Resources\TourResource;
use App\Models\Role;
use Tests\TestCase;
use App\Models\Tour;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
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

        $response = $this->get(route('api_v1.tour.index', ['travel' => $travel->slug]));

        $response->assertStatus(200);
        $response->assertJsonPath('meta.total', count($tours));
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJson(['data' => $expectedResponseData]);
    }

    public function test_tours_can_be_filtered_by_price(): void {
        $travel = Travel::factory()->create();
        $consistentDates = ['starting_date' => now()->format('Y-m-d'), 'ending_date' => now()->addDays($travel->number_of_days)->format('Y-m-d')];
        $tourWithPricePriceHighestThanFilter = Tour::factory()->create(array_merge(['travel_id' => $travel->id, 'price' => 11], $consistentDates));
        $tourWithPricePriceLowestThanFilter = Tour::factory()->create(array_merge(['travel_id' => $travel->id, 'price' => 4], $consistentDates));
        $tourWithPriceAccordingToFilter = Tour::factory()->create(array_merge(['travel_id' => $travel->id, 'price' => 5], $consistentDates));

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours?price_from=5&price_to=10");

        $response
        ->assertStatus(200)
        ->assertJsonMissing(['price' => '1100.00'])
        ->assertJsonMissing(['price' => '400.00'])
        ->assertJsonFragment(['price' => '500.00']);
    }

    public function test_tours_can_be_filtered_by_date(): void {
        $travel = Travel::factory()->create(['number_of_days' => 5]);
        $tourDatesAccordingToFilter = [
            'starting_date' => '2020-05-05',
            'ending_date' => '2020-05-10',
        ];
        $tourDatesNotAccordingToFilter = [
            'starting_date' => '1990-10-10',
            'ending_date' => '1990-10-15',
        ];
        $tourWithDateHighestThanFilter = Tour::factory()->create(array_merge(['travel_id' => $travel->id], $tourDatesNotAccordingToFilter));
        $tourWithDateLowestThanFilter = Tour::factory()->create(array_merge(['travel_id' => $travel->id], $tourDatesNotAccordingToFilter));
        $tourWithDateAccordingToFilter = Tour::factory()->create(array_merge(['travel_id' => $travel->id], $tourDatesAccordingToFilter));

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours?date_from=2020-05-05&date_to=2020-05-10");

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'starting_date' => '2020-05-05',
                'ending_date' => '2020-05-10',
            ]);
    }

    public function test_tours_can_be_sorted_by_price(): void {
        $travel = Travel::factory()->create();
        $toursDates = [
            'starting_date' => now()->format('Y-m-d'),
            'ending_date' => now()->addDays($travel->number_of_days)
        ];
        $tourWithLowestPrice = Tour::factory()->create(array_merge([
            'travel_id' => $travel->id, 'price' => 1
        ], $toursDates));
        $tourWithIntermediaryPrice = Tour::factory()->create(array_merge([
            'travel_id' => $travel->id, 'price' => 2
        ], $toursDates));
        $tourWithHighestPrice = Tour::factory()->create(array_merge([
            'travel_id' => $travel->id, 'price' => 3
        ], $toursDates));

        $response = $this->get("/api/v1/travels/$travel->slug/tours?sort_by_price=desc");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.price', '300.00')
            ->assertJsonPath('data.1.price', '200.00')
            ->assertJsonPath('data.2.price', '100.00');
    }

    public function test_tours_can_be_sorted_by_starting_date(): void {
        $travel = Travel::factory()->create();
        $tourWithLatestStartingDate = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 1,
            'starting_date' => now()->format('Y-m-d'),
            'ending_date' => now()->addDays($travel->number_of_days),
        ]);
        $tourWithOldestStartingDate = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 1,
            'starting_date' => now()->subDays($travel->number_of_days)->format('Y-m-d'),
            'ending_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->get("/api/v1/travels/$travel->slug/tours?sort_by_price=desc");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.starting_date', now()->subDays($travel->number_of_days)->format('Y-m-d'))
            ->assertJsonPath('data.1.starting_date', now()->format('Y-m-d'));
    }

    public function test_guest_cannot_create_tour(): void {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->make();

        $response = $this->postJson("/api/v1/travels/{$travel->id}/tour", array_merge($tour->toArray(), ['travel_id' => $travel->id]));

        $response->assertStatus(401);
    }

    public function test_non_admin_cannot_create_tour(): void {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->make();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/travels/{$travel->id}/tour", array_merge($tour->toArray(), ['travel_id' => $travel->id]));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_tour(): void {
        $this->seed(RoleSeeder::class);
        $travel = Travel::factory()->create(['number_of_days' => 1]);
        $tour = Tour::factory()->make(['starting_date' => '2004-12-24', 'ending_date' => '2004-12-25']);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->first()->id);

        $response = $this->actingAs($user)->postJson("/api/v1/travels/{$travel->id}/tour", array_merge($tour->toArray(), ['travel_id' => $travel->id]));

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $tour->name]);
    }

    public function test_cannot_create_tour_with_invalid_travel(): void {
        $this->seed(RoleSeeder::class);
        $nonExistentTravelId = 12345678;
        $tour = Tour::factory()->make(['travel_id' => $nonExistentTravelId]);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->first()->id);

        $response = $this->actingAs($user)->postJson("/api/v1/travels/{$nonExistentTravelId}/tour", array_merge($tour->toArray(), ['travel_id' => $nonExistentTravelId]));

        $response->assertStatus(404);
    }
}
