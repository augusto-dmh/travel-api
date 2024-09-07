<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_create_user_command_creates_a_user(): void
    {
        $this->artisan('app:create-user', [
            'name' => 'Augusto',
            'email' => 'augusto@email.com',
            'password' => '123456'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'augusto@email.com']);
    }

    public function test_user_can_be_created_by_authenticated_admin(): void {
        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole->id);

        $response = $this->actingAs($user)->postJson('api/v1/create-user', [
            'name' => 'Augusto',
            'email' => 'augusto@email.com',
            'password' => '123456'
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.email', 'augusto@email.com');
    }

    public function test_user_cannot_be_created_by_non_admin(): void {
        $user = User::factory()->create();
        $this->seed(RoleSeeder::class);
        $roles = Role::all();
        foreach ($roles as $role) {
            if ($role->name === 'admin') continue;
            $user->roles()->attach($role->id);
        }

        $response = $this->actingAs($user)->postJson('api/v1/create-user', [
            'name' => 'Augusto',
            'email' => 'augusto@email.com',
            'password' => '123456'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_be_created_by_guest(): void {
        $response = $this->postJson('api/v1/create-user', [
            'name' => 'Augusto',
            'email' => 'augusto@email.com',
            'password' => '123456'
        ]);

        $response->assertStatus(401);
    }
}
