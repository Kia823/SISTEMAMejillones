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
        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->dropForeign(['id_custodio']);
            $table->dropColumn('id_custodio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activos_fijos', function (Blueprint $table) {
            $table->foreignId('id_custodio')->constrained('usuarios')->onDelete('cascade');
        });
    }
};
