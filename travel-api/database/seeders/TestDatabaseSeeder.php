<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

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

            if ($role->name === 'admin') {
                DB::table('role_user')->insert([ // implement this logic of associating an 'admin' role to a user automatically associates an 'editor' role to them in a trigger.
                    'role_id' => Role::where('name', 'editor')->first()->id,
                    'user_id' => $user->id
                ]);
            }

            DB::table('role_user')->insert([
                'role_id' => $role->id,
                'user_id' => $user->id
            ]);

        }

    }
}
