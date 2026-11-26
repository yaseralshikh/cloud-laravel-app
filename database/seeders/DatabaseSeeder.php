<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LaratrustSeeder::class);
        // User::factory(10)->create();

        $user =User::firstOrCreate(
            ['email' => 'yaseralshikh@gmail.com'],
            [
                'name' => 'Yaser Alshikh',
                'password' => '123123123',
                'email_verified_at' => now(),
            ]
        );

        $user->addRole('superadmin');
    }
}
