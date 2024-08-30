<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\RoleUser;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_admin_user_is_also_editor(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $admin_role = Role::where('name', 'admin')->first();
        $editor_role = Role::where('name', 'editor')->first();

        $user->assignRole($admin_role);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $admin_role->id,
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $editor_role->id,
        ]);
    }
}
