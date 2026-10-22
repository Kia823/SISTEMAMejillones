<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanAssets extends Command
{
    protected $signature = 'assets:clean';
    protected $description = 'Elimina todos los activos y registros relacionados (transferencias, verificación)';

    public function handle(): int
    {
        if (! $this->confirm('¿Está seguro? Esto eliminará TODOS los activos y sus registros de transferencia.')) {
            return self::FAILURE;
        }

        DB::beginTransaction();
        try {
            DB::table('acta_transferencia_activo')->delete();
            DB::table('actas_transferencia')->delete();
            $deleted = DB::table('activos_fijos')->delete();
            DB::commit();

            $this->info("✅ Se eliminaron {$deleted} activo(s) y sus registros relacionados.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
