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
        Schema::table('actas_baja', function (Blueprint $table) {
            $table->string('causal_baja')->nullable()->after('motivo');
            $table->string('foto_evidencia')->nullable()->after('archivo_resolucion');
            $table->string('responsable_nombre')->nullable()->after('foto_evidencia');
            $table->string('responsable_ci')->nullable()->after('responsable_nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas_baja', function (Blueprint $table) {
            $table->dropColumn(['causal_baja', 'foto_evidencia', 'responsable_nombre', 'responsable_ci']);
        });
    }
};
