<?php

namespace Tests\Feature;

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
}
