<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\DB;

class VehicleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehicle_models')->delete(); // Opcional: Limpa a tabela antes de popular

        $modelsByVehicleBrand = [
            'Fiat' => ['Mobi', 'Argo', 'Cronos', 'Strada', 'Toro', 'Pulse', 'Fastback', 'Scudo', 'Ducato'],
            'Volkswagen' => ['Polo', 'Nivus', 'T-Cross', 'Virtus', 'Taos', 'Amarok', 'Saveiro', 'Delivery Express', 'Constellation'],
            'Chevrolet' => ['Onix', 'Onix Plus', 'Tracker', 'Montana', 'S10', 'Spin', 'Equinox'],
            'Ford' => ['Ranger', 'Territory', 'Bronco Sport', 'Transit'], // Modelos atuais/relevantes
            'Renault' => ['Kwid', 'Stepway', 'Logan', 'Duster', 'Oroch', 'Master'],
            'Hyundai' => ['HB20', 'HB20S', 'Creta', 'HR'], // HR é um utilitário leve
            'Toyota' => ['Corolla', 'Corolla Cross', 'Yaris Hatch', 'Yaris Sedan', 'Hilux', 'SW4', 'Hiace'],
            'Honda' => ['City Hatch', 'City Sedan', 'HR-V', 'ZR-V', 'Civic'],
            'Jeep' => ['Renegade', 'Compass', 'Commander', 'Gladiator'],
            'Peugeot' => ['208', '2008', 'Partner Rapid', 'Boxer'],
            'Citroën' => ['C3', 'C4 Cactus', 'Jumpy', 'Jumper'],
            'Mitsubishi' => ['L200 Triton', 'Eclipse Cross', 'Outlander Sport'], // Pajero Sport
            'Nissan' => ['Versa', 'Kicks', 'Frontier'],
            'Caoa Chery' => ['Tiggo 5x Pro', 'Tiggo 7 Pro', 'Tiggo 8', 'Arrizo 6'],
            'Kia' => ['Sportage', 'Stonic', 'Niro', 'K2500'], // K2500 é um utilitário
            'BMW' => ['Série 3', 'X1', 'Série 1'],
            'Mercedes-Benz' => ['Classe C', 'GLA', 'Sprinter', 'Accelo', 'Atego'], // Incluindo utilitários/caminhões
            'Audi' => ['A3 Sedan', 'Q3', 'A4'],
            'Volvo' => ['XC40', 'XC60', 'FH', 'VM'], // Incluindo caminhões
            'Land Rover' => ['Discovery Sport', 'Defender', 'Range Rover Evoque'],
            'Ram' => ['Rampage', '1500', 'Classic', '2500'],
            'GWM' => ['Haval H6', 'Ora 03'],
            'Scania' => ['P-Series', 'G-Series', 'R-Series', 'S-Series'], // Caminhões
            'Iveco' => ['Daily', 'Tector', 'S-Way'], // Utilitários e Caminhões
            'Agrale' => ['Marruá', 'A8700', 'MT17.0'], // Utilitários, chassis de ônibus
            'Yamaha' => ['NMAX', 'XTZ 150 Crosser', 'Factor 150'], // Motos
        ];

        foreach ($modelsByVehicleBrand as $brandName => $models) {
            $brand = VehicleBrand::where('name', $brandName)->first();

            if ($brand) {
                foreach ($models as $modelName) {
                    VehicleModel::create([
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                    ]);
                }
            } else {
                $this->command->warn("Marca não encontrada no seeder: " . $brandName . ". Modelos não foram criados para esta marca.");
            }
        }
    }
}
