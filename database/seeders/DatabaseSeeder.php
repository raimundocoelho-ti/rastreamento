<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            DepartmentSeeder::class,
            VehicleTypeSeeder::class,
            VehicleBrandSeeder::class,
            VehicleModelSeeder::class,
            VehicleSeeder::class,
            VehicleLocationSeeder::class
        ]);


    }
}
