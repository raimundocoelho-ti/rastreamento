<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Necessário para usar DB::statement

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade'); // Relacionamento com vehicles

            // Adiciona a coluna GEOMETRY para armazenar latitude e longitude
            // SRID 4326 é para coordenadas WGS 84 (GPS)
            $table->geometry('location', 'POINT', 4326); // Tipo de dado espacial: POINT com SRID 4326

            // Campos adicionais para um rastreamento mais completo
            $table->float('speed')->nullable()->comment('Velocidade em km/h');
            $table->smallInteger('heading')->nullable()->comment('Direção em graus (0-359), 0=Norte');
            $table->float('altitude')->nullable()->comment('Altitude em metros');
            $table->unsignedBigInteger('tracking_session_id')->nullable()->comment('ID da sessão de rastreamento, para agrupar pontos de uma viagem');

            $table->timestamps(); // created_at e updated_at
        });

        // Adiciona um índice espacial (GiST) para a coluna 'location' para otimizar consultas espaciais
        DB::statement('CREATE INDEX vehicle_locations_location_idx ON vehicle_locations USING GIST (location);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove o índice antes de apagar a tabela, se existir
        DB::statement('DROP INDEX IF EXISTS vehicle_locations_location_idx;');
        Schema::dropIfExists('vehicle_locations');
    }
};
