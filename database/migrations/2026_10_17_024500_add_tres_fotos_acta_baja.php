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
            $table->string('foto_evidencia_1')->nullable()->after('foto_evidencia');
            $table->string('foto_evidencia_2')->nullable()->after('foto_evidencia_1');
            $table->string('foto_evidencia_3')->nullable()->after('foto_evidencia_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actas_baja', function (Blueprint $table) {
            $table->dropColumn(['foto_evidencia_1', 'foto_evidencia_2', 'foto_evidencia_3']);
        });
    }
};
