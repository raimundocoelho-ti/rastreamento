<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Se o teu modelo User não tratar automaticamente o hashing da password através de casts,
// irias precisar de: use Illuminate\Support\Facades\Hash;
// Mas como o teu User.php tem 'password' => 'hashed' nos casts, o modelo trata disso.

class UserSeeder extends Seeder
{
    protected static ?string $password;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@exemplo.com',
                'role' => UserRole::ADMIN->value,
                'password' => static::$password ??= Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Gerente Exemplo',
                'email' => 'manager@exemplo.com',
                'role' => UserRole::MANAGER->value,
                'password' => static::$password ??= Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Operador Exemplo',
                'email' => 'operator@exemplo.com',
                'role' => UserRole::OPERATOR->value,
                'password' => static::$password ??= Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Motorista Exemplo',
                'email' => 'driver@exemplo.com',
                'role' => UserRole::DRIVER->value,
                'password' => static::$password ??= Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($usersData as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Critério para encontrar o utilizador existente
                $userData                        // Dados para criar ou atualizar
            );
        }
    }
}
