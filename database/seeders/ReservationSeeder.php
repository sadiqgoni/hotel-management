<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation; // Import your Reservation model
use App\Models\Guest;
use App\Models\Room;
use Faker\Factory as Faker;


class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Assuming you have seeded rooms and guests before seeding reservations
        $guestIds = Guest::pluck('id')->toArray();
        $roomIds = Room::pluck('id')->toArray();

        // Create 50 fake reservation entries
        foreach (range(1, 50) as $index) {
            Reservation::create([
                'guest_id' => $faker->randomElement($guestIds),
                'room_id' => $faker->randomElement($roomIds),
                'check_in_date' => $faker->dateTimeBetween('-1 month', 'now'),
                'check_out_date' => $faker->dateTimeBetween('now', '+1 week'),
                'total_amount' => $faker->randomFloat(2, 1000, 5000),
                'amount_paid' => $faker->randomFloat(2, 500, 5000),
                'payment_method' => $faker->randomElement(['cash', 'card','mobile']),
                'coupon_discount' => $faker->randomFloat(2, 0, 500),
                'payment_status' => $faker->randomElement(['Partial Payment', 'Full Payment',]),
                'price_per_night' => $faker->randomFloat(2, 100, 1000),
                'frequent_guest_message' => $faker->sentence(),
                'number_of_nights' => $faker->numberBetween(1, 7),
                'status' => $faker->randomElement(['Confirmed', 'On Hold', ]),
                'special_requests' => $faker->sentence(10),
                'number_of_people' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
