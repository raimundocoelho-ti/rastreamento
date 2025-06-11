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
            // Comuns no Brasil (Carros e Leves)
            ['name' => 'Fiat'],
            ['name' => 'Volkswagen'],
            ['name' => 'Chevrolet'],
            ['name' => 'Ford'],
            ['name' => 'Renault'],
            ['name' => 'Hyundai'],
            ['name' => 'Toyota'],
            ['name' => 'Honda'],
            ['name' => 'Jeep'],
            ['name' => 'Peugeot'],
            ['name' => 'Citroën'],
            ['name' => 'Mitsubishi'],
            ['name' => 'Nissan'],
            ['name' => 'Caoa Chery'],
            ['name' => 'Kia'],
            ['name' => 'BMW'],
            ['name' => 'Mercedes-Benz'], // Pode ser separado para carros e pesados se necessário
            ['name' => 'Audi'],
            ['name' => 'Volvo'], // Pode ser separado para carros e pesados
            ['name' => 'Land Rover'],
            ['name' => 'Ram'],
            ['name' => 'GWM'],
            // Marcas de Pesados / Utilitários comuns em Prefeituras
            ['name' => 'Scania'],
            ['name' => 'Iveco'],
            ['name' => 'Agrale'],
            // Motocicletas (comuns em algumas frotas de prefeitura)
            ['name' => 'Yamaha'],
            // Adicione mais marcas conforme necessário
        ];

        foreach ($brands as $brand) {
            VehicleBrand::create($brand);
        }
    }
}
