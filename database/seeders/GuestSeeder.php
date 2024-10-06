<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest; // Import your Guest model
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create 50 fake guest entries
        foreach (range(1, 50) as $index) {
            Guest::create([
                'name' => $faker->name,
                'phone_number' => $faker->phoneNumber,
                'preferences' => $faker->sentence(6),
                'nin_number' => $faker->numerify('##############'), // Fake NIN
                'bonus_code' => $faker->bothify('BONUS-###???'), // Fake bonus code
                'stay_count' => $faker->numberBetween(0, 10),
            ]);
        }
    }
}
