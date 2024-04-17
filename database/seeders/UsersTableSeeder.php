<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run()
    {
        $faker = Faker::create();

        // Define the number of users to create
        $numberOfUsers = 1;

        // Create 100 random users
        User::factory($numberOfUsers)->create([
            'email_verified_at' => now(),
            'password' => Hash::make('testtest'),
            'remember_token' => "token",
            'sex' => $this->getRandomSex($faker),
            'name' => $this->getRandomName($faker),
            'infix' => "",
            'last_name' => $this->getRandomLastName($faker),
            'birth_date' => $this->getRandomBirthDate($faker),
            'street' => $this->getRandomStreet($faker),
            'postal_code' => $this->getRandomPostalCode($faker),
            'city' => $this->getRandomCity($faker),
            'phone' => $this->getRandomPhone($faker),
            'avg' => rand(0, 1),
            'member_date' => now(),
            'dolfijnen_name' => $this->getRandomDolfijnenName($faker)
        ]);
    }


    // Helper functions to generate random data
    private function getRandomSex()
    {
        return rand(0, 1) ? 'Male' : 'Female';
    }

    private function getRandomName()
    {
        return $this->faker->firstName;
    }

    private function getRandomLastName()
    {
        return $this->faker->lastName;
    }

    private function getRandomBirthDate()
    {
        return $this->faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d');
    }

    private function getRandomStreet()
    {
        return $this->faker->streetAddress;
    }

    private function getRandomPostalCode()
    {
        return $this->faker->postcode;
    }

    private function getRandomCity()
    {
        return $this->faker->city;
    }

    private function getRandomPhone()
    {
        return $this->faker->phoneNumber;
    }

    private function getRandomDolfijnenName()
    {
        return $this->faker->word;
    }
}
