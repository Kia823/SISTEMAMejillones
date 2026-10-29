<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('usuarios')) {
            DB::table('usuarios')->update([
                'nombre_completo' => DB::raw('UPPER(nombre_completo)'),
            ]);
        }

        if (Schema::hasTable('carreras')) {
            DB::table('carreras')
                ->where('codigo', 'AGP')
                ->update(['codigo' => 'AGRO']);
        }
    }

    public function down(): void
    {
        // La conversión a mayúsculas no puede revertirse sin el valor original.
    }
};
