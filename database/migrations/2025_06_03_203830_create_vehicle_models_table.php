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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')
                ->constrained('vehicle_brands')
                ->onDelete('restrict'); // Modelo não pode ser órfão de marca, e marca não deleta se tiver modelo
            $table->string('name');
            $table->timestamps();

            $table->unique(['brand_id', 'name']); // Nome do modelo único por marca
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
