<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $hashedPassword = Hash::make('password');

        $users = [
            ['email' => "test@waterschoutingmhg.nl",
            'email_verified_at' => now(),
            'password' => $hashedPassword,
            'remember_token' => "token",
            'created_at' => now(),
            'updated_at' => now(),
            'sex' => "Male",
            'name' => "Test",
            'infix' => "van",
            'last_name' => "Dijk",
            'birth_date' => "1999-01-01",
            'street' => "Teststraat",
            'postal_code' => "1234AB",
            'city' => "Teststad",
            'phone' => "0612345678",
            'avg' => 0,
            'profile_picture' => "",
            'member_date' => now(),
            'dolfijnen_name' => "Dolfijn"]
        ];

        DB::table('users')->insert($users);

    }
}
