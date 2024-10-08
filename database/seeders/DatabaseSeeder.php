<?php

namespace Database\Seeders;

use App\Models\CarMake;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Prashant Rijal',
            'email' => 'admin@log.in',
            'password' => bcrypt('12345678'),
            'phone' => '1234567890',
        ]);

        foreach (range(1, 10) as $index) {
            $make = CarMake::create([
                'name' => 'Car Make ' . $index,
            ]);

            foreach (range(1, 10) as $index) {
                $make->carModels()->create([
                    'name' => 'Car Model ' . $index,
                ]);
            }
        }
    }
}
