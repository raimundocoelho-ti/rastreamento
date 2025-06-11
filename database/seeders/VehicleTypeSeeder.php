<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_types')->delete(); // Opcional: Limpa a tabela

        $types = [
            [
                'name' => 'Carro Sedan',
                'description' => 'Veículo de passeio com 4 portas e porta-malas separado.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Carro Hatch',
                'description' => 'Veículo de passeio compacto com porta-malas integrado.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Picape Leve',
                'description' => 'Veículo utilitário leve com caçamba.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Picape Média',
                'description' => 'Veículo utilitário médio com caçamba e maior capacidade.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'SUV',
                'description' => 'Veículo Utilitário Esportivo.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Van/Furgão',
                'description' => 'Veículo para transporte de passageiros ou carga.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Ambulância',
                'description' => 'Veículo de emergência médica.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Ônibus Escolar',
                'description' => 'Veículo para transporte de estudantes.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Micro-ônibus',
                'description' => 'Veículo para transporte coletivo de passageiros.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
            [
                'name' => 'Caminhão Leve (VUC)',
                'description' => 'Veículo Urbano de Carga.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => true, // Alguns podem ter horímetro também
            ],
            [
                'name' => 'Caminhão Basculante',
                'description' => 'Caminhão com caçamba basculante para transporte de materiais.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => true,
            ],
            [
                'name' => 'Retroescavadeira',
                'description' => 'Máquina pesada para escavação e carregamento.',
                'requires_license_plate' => false,
                'controlled_by_odometer' => false,
                'controlled_by_hour_meter' => true,
            ],
            [
                'name' => 'Motoniveladora (Patrol)',
                'description' => 'Máquina pesada para nivelamento de terreno.',
                'requires_license_plate' => false,
                'controlled_by_odometer' => false,
                'controlled_by_hour_meter' => true,
            ],
            [
                'name' => 'Pá Carregadeira',
                'description' => 'Máquina pesada para carregamento de materiais.',
                'requires_license_plate' => false,
                'controlled_by_odometer' => false,
                'controlled_by_hour_meter' => true,
            ],
            [
                'name' => 'Trator Agrícola',
                'description' => 'Veículo agrícola para diversas operações.',
                'requires_license_plate' => false,
                'controlled_by_odometer' => false,
                'controlled_by_hour_meter' => true,
            ],
            [
                'name' => 'Motocicleta',
                'description' => 'Veículo de duas rodas para transporte ágil.',
                'requires_license_plate' => true,
                'controlled_by_odometer' => true,
                'controlled_by_hour_meter' => false,
            ],
        ];

        foreach ($types as $type) {
            VehicleType::create($type);
        }
    }
}
