<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User; // Para buscar um gestor exemplo
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->delete(); // Opcional: Limpa a tabela

        // Tenta buscar um usuário para ser gestor, se não existir, manager_id será null
        $manager = User::orderBy('id')->first();

        $departments = [
            ['name' => 'Secretaria de Administração', 'description' => 'Responsável pela gestão administrativa.', 'manager_id' => $manager?->id],
            ['name' => 'Secretaria de Obras', 'description' => 'Responsável por obras e infraestrutura.', 'manager_id' => null],
            ['name' => 'Secretaria de Saúde', 'description' => 'Gestão dos serviços de saúde municipais.', 'manager_id' => $manager?->id],
            ['name' => 'Secretaria de Educação', 'description' => 'Gestão dos serviços de educação.', 'manager_id' => null],
            ['name' => 'Secretaria de Transporte', 'description' => 'Gerenciamento da frota e logística de transporte.', 'manager_id' => $manager?->id],
            ['name' => 'Guarda Municipal', 'description' => 'Segurança pública e patrimonial.', 'manager_id' => null],
            ['name' => 'Defesa Civil', 'description' => 'Prevenção e resposta a desastres.', 'manager_id' => $manager?->id],
            ['name' => 'Gabinete do Prefeito', 'description' => 'Assessoria direta ao prefeito.', 'manager_id' => null],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
