<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vehicles')->delete();

        $vehicleModels = VehicleModel::all();
        $vehicleTypes = VehicleType::all();

        if ($vehicleModels->isEmpty() || $vehicleTypes->isEmpty()) {
            $this->command->warn("Seeders de VehicleModel ou VehicleType precisam ser executados primeiro ou não possuem dados. Veículos não foram criados.");
            return;
        }

        $colors = ['Branco', 'Prata', 'Preto', 'Cinza', 'Vermelho', 'Azul', 'Amarelo', 'Verde', 'Marrom'];
        $statuses = ['Ativo', 'Em Manutenção', 'Inativo', 'Baixado'];

        for ($i = 0; $i < 30; $i++) {
            $selectedVehicleModel = $vehicleModels->random();
            $selectedVehicleType = $vehicleTypes->random();

            $licensePlate = null;
            if ($selectedVehicleType->requires_license_plate) {
                $isMercosul = (bool)random_int(0, 1);
                if ($isMercosul) {
                    $letras1 = strtoupper(Str::random(3));
                    $numero1 = random_int(0, 9);
                    $letraMeio = chr(random_int(65, 90));
                    $numerosFinais = str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT);
                    $licensePlate = $letras1 . $numero1 . $letraMeio . $numerosFinais;
                } else {
                    $letras = strtoupper(Str::random(3));
                    $numeros = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                    $licensePlate = $letras . '-' . $numeros;
                }
            }

            $numberOfSeats = match ($selectedVehicleType->name) {
                'Motocicleta' => random_int(1, 2),
                'Carro Sedan', 'Carro Hatch', 'SUV' => random_int(4, 5),
                'Picape Leve' => random_int(2, 5),
                'Picape Média' => random_int(2, 5),
                'Van/Furgão' => random_int(2, 16),
                'Ambulância' => random_int(3, 5),
                'Micro-ônibus' => random_int(15, 30),
                'Ônibus Escolar' => random_int(20, 60),
                default => random_int(1, 5),
            };
            if ($selectedVehicleType->controlled_by_hour_meter && !$selectedVehicleType->controlled_by_odometer) {
                $numberOfSeats = 1;
            }

            Vehicle::create([
                'license_plate' => $licensePlate,
                'vehicle_model_id' => $selectedVehicleModel->id,
                'vehicle_type_id' => $selectedVehicleType->id,
                'color' => $colors[array_rand($colors)],
                'number_of_seats' => $numberOfSeats,
                'status' => $statuses[array_rand($statuses)],
                'notes' => 'Veículo de teste nº ' . ($i + 1) . '.',
                'image' => null,
            ]);
        }
    }
}
