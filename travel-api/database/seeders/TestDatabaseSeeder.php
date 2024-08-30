<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TourSeeder;
use Database\Seeders\TravelSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        $this->call([RoleSeeder::class, TravelSeeder::class, TourSeeder::class]);

        foreach ($users as $user) {
            $role = Role::inRandomOrder()->first();

            $user->assignRole($role);
        }

    }
}
