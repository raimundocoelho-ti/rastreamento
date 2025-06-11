<?php

namespace Database\Seeders;

use App\Models\VehicleBrand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_brands')->delete(); // Opcional: Limpa a tabela antes de popular

        $brands = [
            ['name' => 'Fiat'],
            ['name' => 'Volkswagen'],
            ['name' => 'Renault'],
            ['name' => 'Citroen'],
        ];

        foreach ($brands as $brand) {
            VehicleBrand::create($brand);
        }
    }
}
