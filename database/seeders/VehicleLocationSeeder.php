<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleLocation;
use Carbon\Carbon; // Para manipular datas

class VehicleLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garantir que temos alguns veículos para associar as localizações
        // Se você já tem um VehicleSeeder, pode garantir que ele roda antes
        if (Vehicle::count() === 0) {
            $this->call(VehicleSeeder::class); // Chame seu seeder de veículos, se existir
            // Ou crie alguns veículos simples aqui para teste
            Vehicle::factory()->count(5)->create();
        }

        $vehicles = Vehicle::all();

        // Coordenadas base para simular uma área (ex: São Paulo)
        $baseLat = -23.55052; // Centro de São Paulo
        $baseLng = -46.63330;

        foreach ($vehicles as $vehicle) {
            $trackingSessionId = now()->timestamp; // ID da sessão para agrupar pontos de uma 'viagem'

            // Simular uma "viagem" com 5 a 10 pontos de localização para cada veículo
            $numLocations = rand(5, 10);
            $currentLat = $baseLat + (rand(-100, 100) / 10000.0); // Pequena variação
            $currentLng = $baseLng + (rand(-100, 100) / 10000.0);
            $currentTime = Carbon::now()->subMinutes(rand(0, 60)); // Começa em algum momento no passado recente

            for ($i = 0; $i < $numLocations; $i++) {
                // Pequena variação para simular movimento
                $currentLat += (rand(-10, 10) / 10000.0);
                $currentLng += (rand(-10, 10) / 10000.0);
                $currentTime->addSeconds(rand(30, 120)); // A cada 30s a 2min

                VehicleLocation::create([
                    'vehicle_id' => $vehicle->id,
                    'location' => [
                        'latitude' => $currentLat,
                        'longitude' => $currentLng,
                    ],
                    'speed' => rand(0, 80) + (rand(0, 99) / 100), // Velocidade aleatória (0 a 80 km/h)
                    'heading' => rand(0, 359), // Direção aleatória
                    'altitude' => rand(0, 1000) + (rand(0, 99) / 100), // Altitude aleatória
                    'tracking_session_id' => $trackingSessionId,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ]);
            }

            // Opcional: Adicionar a última localização mais recente para cada veículo
            // (simula a posição atual)
            VehicleLocation::create([
                'vehicle_id' => $vehicle->id,
                'location' => [
                    'latitude' => $currentLat + (rand(-5, 5) / 100000.0), // Último ponto, bem próximo
                    'longitude' => $currentLng + (rand(-5, 5) / 100000.0),
                ],
                'speed' => rand(0, 60) + (rand(0, 99) / 100),
                'heading' => rand(0, 359),
                'altitude' => rand(0, 1000) + (rand(0, 99) / 100),
                'tracking_session_id' => $trackingSessionId, // Ainda faz parte da mesma sessão, ou uma nova
                'created_at' => Carbon::now(), // Última localização é 'agora'
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
