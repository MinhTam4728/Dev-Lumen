<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CustomerSeeder::class); // Gọi seeder CustomerSeeder
        $this->call(OrderSeeder::class);    // Gọi seeder OrderSeeder
    }
}
