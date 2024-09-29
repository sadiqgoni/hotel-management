<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Table::create(['name' => 'Table 1', 'seats' => 4, 'is_available' => true]);
        Table::create(['name' => 'Table 2', 'seats' => 2, 'is_available' => true]);
        Table::create(['name' => 'Table 3', 'seats' => 6, 'is_available' => false]);
    }
}
