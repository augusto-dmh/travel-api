<?php

namespace Tests\Feature;

use App\Http\Resources\TravelResource;
use Tests\TestCase;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $travel = Travel::factory()->create();

        $this->assertDatabaseHas('travels', [
            'is_public' => $travel->is_public,
            'slug' => $travel->slug,
            'name' => $travel->name,
            'description' => $travel->description,
            'number_of_days' => $travel->number_of_days,
        ]);
    }

    public function test_public_paginated_travels_are_returned(): void {
        $travels = Travel::factory()->count(15)->create();
        $publicTravels = $travels->filter(fn($travel) => $travel->is_public);
        $expectedResponseData = TravelResource::collection($publicTravels)->resolve();

        $response = $this->get(route('api_v1.travel.index'));

        $response->assertStatus(200);
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJsonPath('meta.total', count($publicTravels));
        $response->assertJson(['data' => $expectedResponseData]);
    }
}
