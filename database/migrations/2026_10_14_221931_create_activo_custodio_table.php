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
        Schema::create('activo_custodio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_activo')->constrained('activos_fijos', 'id_activo')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->timestamps();
            
            // Índice compuesto para evitar duplicados
            $table->unique(['id_activo', 'id_usuario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activo_custodio');
    }
};
