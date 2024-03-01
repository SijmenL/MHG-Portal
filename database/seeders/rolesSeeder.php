<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class rolesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $rollen = [
            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Dolfijnen Leiding",
            'description' => "Leiding van de Dolfijnen"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Zeeverkenners Leiding",
            'description' => "Leiding van de Zeeverkenners"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Loodsen Stamoudste",
            'description' => "Stamoudste van de Loodsen"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Afterloodsen Organisator",
            'description' => "Organisator van de Afterloodsen"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Vrijwilliger",
            'description' => "Vrijwilliger van de groep"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Administratie",
            'description' => "Administratie van de groep"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Bestuur",
            'description' => "Bestuur van de groep"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Ouderraad",
            'description' => "Ouderraad van de groep"],
        ];

        DB::table('roles')->insert($rollen);
    }
}
