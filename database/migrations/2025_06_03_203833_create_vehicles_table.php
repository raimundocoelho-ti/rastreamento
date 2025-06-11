<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate', 15)->unique()->nullable();
            $table->string('color', 50)->nullable();
            $table->unsignedTinyInteger('number_of_seats')->nullable();
            $table->string('status', 50)->default('Ativo');
            $table->text('notes')->nullable();
            $table->string('image')->nullable(); // Campo de imagem adicionado

            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->onDelete('restrict');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
