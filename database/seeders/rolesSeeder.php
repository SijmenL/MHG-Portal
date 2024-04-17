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
                'role' => "Dolfijnen Hoofdleiding",
                'description' => "Hoofdleiding van de Dolfijnen"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Dolfijnen Penningmeester",
                'description' => "Penningmeester van de Dolfijnen"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Dolfijn",
                'description' => "Dolfijnen lid"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Zeeverkenners Leiding",
            'description' => "Leiding van de Zeeverkenners"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Zeeverkenners Hoofdleiding",
                'description' => "Hoofdleiding van de Zeeverkenners"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Zeeverkenners Penningmeester",
                'description' => "Penningmeester van de Zeeverkenners"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Zeeverkenner",
                'description' => "Zeeverkenner lid"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Loodsen Stamoudste",
            'description' => "Stamoudste van de Loodsen"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Loodsen Penningmeester",
                'description' => "Penningmeester van de Loodsen"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Loods",
                'description' => "Loodsen lid"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Afterloodsen Organisator",
            'description' => "Organisator van de Afterloodsen"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Afterloodsen Voorzitter",
            'description' => "Voorzitter van de Afterloodsen"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Afterloods",
                'description' => "Afterloodsen lid"],

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
            'description' => "Bestuur"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Secretaris",
                'description' => "Secretaris"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Penningmeester",
                'description' => "Penningmeester"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Voorzitter",
                'description' => "Voorzitter"],
            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Vice-voorzitter",
                'description' => "Vice-voorzitter"],

            ['created_at' => now(),
            'updated_at' => now(),
            'role' => "Ouderraad",
            'description' => "Ouderraad van de groep"],

            ['created_at' => now(),
                'updated_at' => now(),
                'role' => "Praktijkbegeleider",
                'description' => "Mensen die bijvoorbeeld CWO les geven"],
        ];

        DB::table('roles')->insert($rollen);
    }
}
