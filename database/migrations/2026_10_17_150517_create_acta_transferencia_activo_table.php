<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acta_transferencia_activo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_acta_transferencia')
                  ->constrained('actas_transferencia', 'id_acta_transferencia')
                  ->cascadeOnDelete();
            $table->foreignId('id_activo')
                  ->constrained('activos_fijos', 'id_activo')
                  ->cascadeOnDelete();
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            $table->unique(['id_acta_transferencia', 'id_activo'], 'uq_acta_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acta_transferencia_activo');
    }
};
