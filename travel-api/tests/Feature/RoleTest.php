<?php

namespace Tests\Unit;

use App\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    protected $model = Role::class;

    use RefreshDatabase;

    public function test_it_can_be_created(): void
    {
        $role = Role::create(['name' => 'admin']);

        $this->assertDatabaseHas('roles', [
            'name' => 'admin'
        ]);
    }
}
