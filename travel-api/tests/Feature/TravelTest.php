<?php

namespace Tests\Feature;

use App\Http\Resources\TravelResource;
use App\Models\Role;
use Tests\TestCase;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class TravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_can_be_created(): void
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

    function test_it_can_be_created_by_admin(): void {
        $this->seed(RoleSeeder::class);
        $admin_role = Role::where('name', 'admin')->first();
        $user = User::factory()->create();
        $user->roles()->attach($admin_role->id);
        $travel = Travel::factory()->make();

        $response = $this->actingAs($user)->post('/api/v1/travels', $travel->toArray());

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $travel->name]);
        $this->assertDatabaseHas('travels', ['name' => $travel->name]);
    }

    function test_it_cannot_be_created_by_non_admin(): void {
        $user = User::factory()->create();
        $travelData = Travel::factory()->make()->toArray();

        $response = $this->actingAs($user)->post('/api/v1/travels', $travelData);

        $response->assertStatus(403);
    }

    function test_it_cannot_be_created_by_guest(): void {
        $travel_data = Travel::factory()->make()->toArray();

        $response = $this->post('/api/v1/travels', $travel_data);

        $response->assertStatus(302);
    }

    public function test_guest_cannot_update_a_travel(): void {
        $travel = Travel::factory()->create();

        $response = $this->putJson("/api/v1/travels/$travel->id", ['name' => 'new name']);

        $response->assertStatus(401);
    }

    public function test_non_editor_cannot_update_a_travel(): void {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole->id);
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/v1/travels/$travel->id", ['name' => 'new name']);

        $response->assertStatus(403);
    }

    public function test_editor_can_update_travel_with_valid_data(): void {
        $this->seed(RoleSeeder::class);
        $editorRole = Role::where('name', 'editor')->first();
        $user = User::factory()->create();
        $user->roles()->attach($editorRole->id);
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->put("api/v1/travels/$travel->id", [
            'is_public' => false,
            'name' => 'new name',
            'description' => 'new description',
            'number_of_days' => 8,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'new name',
                'description' => 'new description',
                'number_of_days' => 8
            ]);
    }

    public function test_editor_cannot_update_travel_with_invalid_data(): void {
        $this->seed(RoleSeeder::class);
        $editorRole = Role::where('name', 'editor')->first();
        $user = User::factory()->create();
        $user->roles()->attach($editorRole->id);
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->put("api/v1/travels/$travel->id", [
            'is_public' => 'some string',
            'name' => '',
            'description' => '',
            'number_of_days' => 'some string',
        ]);

        $response->assertStatus(422);
    }
}
