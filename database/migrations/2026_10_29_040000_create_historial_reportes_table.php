<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')
                ->nullable()
                ->constrained('usuarios', 'id_usuario')
                ->nullOnDelete();
            $table->string('tipo_reporte', 100);
            $table->string('formato', 10);
            $table->string('nombre_archivo', 255);
            $table->json('filtros')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'formato']);
            $table->index('tipo_reporte');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_reportes');
    }
};
