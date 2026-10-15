<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        // Crear carpeta si no existe
        if (! File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }

        // ⭐ La protección de admin se hace desde routes/web.php con middleware('rol:ADMINISTRADOR')
    }

    // ============================================================
    // INDEX — Listar copias existentes
    // ============================================================
    public function index()
    {
        // ⭐ Doble verificación por seguridad
        if (! Auth::check() || ! Auth::user()->esAdmin()) {
            abort(403, 'Solo el administrador puede gestionar copias de seguridad.');
        }

        $copias = $this->listarCopias();

        $totalCopias = count($copias);
        $espacioTotal = 0;
        foreach ($copias as $c) {
            $espacioTotal += $c['tamano'];
        }

        $ultimaCopia = $copias[0] ?? null;

        $dbName = DB::connection()->getDatabaseName();
        $dbSize = 0;
        try {
            $result = DB::select("
                SELECT SUM(data_length + index_length) AS size
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$dbName]);
            $dbSize = $result[0]->size ?? 0;
        } catch (\Throwable $e) {
            $dbSize = 0;
        }

        return view('backup.index', compact(
            'copias',
            'totalCopias',
            'espacioTotal',
            'ultimaCopia',
            'dbSize',
            'dbName'
        ));
    }

    // ============================================================
    // CREAR COPIA
    // ============================================================
    public function crear(Request $request)
    {
        if (! Auth::check() || ! Auth::user()->esAdmin()) {
            return response()->json(['success' => false, 'mensaje' => 'No autorizado.'], 403);
        }

        try {
            $tipo = $request->input('tipo', 'Manual');
            $resultado = $this->generarBackup($tipo);

            return response()->json([
                'success'  => true,
                'mensaje'  => 'Copia de seguridad creada correctamente.',
                'archivo'  => $resultado['nombre'],
                'tamano'   => $resultado['tamano'],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al crear la copia: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // DESCARGAR
    // ============================================================
    public function descargar($archivo)
    {
        if (! Auth::check() || ! Auth::user()->esAdmin()) {
            abort(403);
        }

        $ruta = $this->backupPath . DIRECTORY_SEPARATOR . $archivo;

        if (! File::exists($ruta)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return response()->download($ruta, $archivo, [
            'Content-Type' => 'application/sql',
        ]);
    }

    // ============================================================
    // ELIMINAR
    // ============================================================
    public function eliminar($archivo)
    {
        if (! Auth::check() || ! Auth::user()->esAdmin()) {
            abort(403);
        }

        $ruta = $this->backupPath . DIRECTORY_SEPARATOR . $archivo;

        if (! File::exists($ruta)) {
            return back()->with('error', 'El archivo no existe.');
        }

        File::delete($ruta);

        return back()->with('success', "Copia '{$archivo}' eliminada correctamente.");
    }

    // ============================================================
    // IMPORTAR
    // ============================================================
    public function importar(Request $request)
    {
        if (! Auth::check() || ! Auth::user()->esAdmin()) {
            abort(403);
        }

        $request->validate([
            'archivo_sql' => 'required|file|mimes:sql,txt|max:51200',
        ], [
            'archivo_sql.required' => 'Debe seleccionar un archivo SQL.',
            'archivo_sql.mimes'    => 'Solo se permiten archivos .sql o .txt',
            'archivo_sql.max'      => 'El archivo no debe superar los 50 MB.',
        ]);

        $archivoTemporal = $request->file('archivo_sql')->getRealPath();
        $contenido = File::get($archivoTemporal);

        if (empty(trim($contenido))) {
            return back()->with('error', 'El archivo SQL está vacío.');
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($contenido);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', 'Base de datos importada correctamente. Verifica los datos.');
        } catch (\Throwable $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Throwable $e2) {}

            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    // ============================================================
    // HELPERS
    // ============================================================

    protected function generarBackup(string $tipo = 'Manual'): array
    {
        $dbName = DB::connection()->getDatabaseName();
        $prefijo = $tipo === 'Auto' ? 'backup_auto_' : 'backup_san_';

        $fecha = Carbon::now()->format('Y_m_d-H_i_s');
        $nombre = "{$prefijo}{$fecha}.sql";
        $ruta = $this->backupPath . DIRECTORY_SEPARATOR . $nombre;

        $tablas = DB::select('SHOW TABLES');
        $propiedadTabla = 'Tables_in_' . $dbName;

        $sql = "-- ============================================\n";
        $sql .= "-- Copia de seguridad: {$nombre}\n";
        $sql .= "-- Base de datos: {$dbName}\n";
        $sql .= "-- Generado: " . Carbon::now()->format('d/m/Y H:i:s') . "\n";
        $sql .= "-- Tipo: {$tipo}\n";
        $sql .= "-- ============================================\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $sql .= "SET time_zone = '+00:00';\n\n";

        foreach ($tablas as $tabla) {
            $nombreTabla = $tabla->$propiedadTabla;

            $crear = DB::select("SHOW CREATE TABLE `{$nombreTabla}`");
            $sql .= "-- --------------------------------------------\n";
            $sql .= "-- Estructura de la tabla `{$nombreTabla}`\n";
            $sql .= "-- --------------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$nombreTabla}`;\n";
            $sql .= $crear[0]->{'Create Table'} . ";\n\n";

            $filas = DB::table($nombreTabla)->get();

            if ($filas->count() > 0) {
                $sql .= "-- Datos de la tabla `{$nombreTabla}`\n";

                foreach ($filas as $fila) {
                    $valores = [];
                    foreach ((array) $fila as $valor) {
                        if (is_null($valor)) {
                            $valores[] = 'NULL';
                        } elseif (is_numeric($valor)) {
                            $valores[] = $valor;
                        } else {
                            $valores[] = "'" . addslashes($valor) . "'";
                        }
                    }
                    $sql .= "INSERT INTO `{$nombreTabla}` VALUES (" . implode(', ', $valores) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($ruta, $sql);

        return [
            'nombre' => $nombre,
            'ruta'   => $ruta,
            'tamano' => File::size($ruta),
        ];
    }

    protected function listarCopias(): array
    {
        $copias = [];

        if (! File::exists($this->backupPath)) {
            return $copias;
        }

        $archivos = File::files($this->backupPath);

        foreach ($archivos as $archivo) {
            if ($archivo->getExtension() !== 'sql') continue;

            $nombre = $archivo->getFilename();

            if (str_starts_with($nombre, 'backup_auto_')) {
                $tipo = 'Auto';
            } elseif (str_starts_with($nombre, 'backup_san_')) {
                $tipo = 'Manual';
            } else {
                $tipo = 'Sistema';
            }

            $copias[] = [
                'nombre' => $nombre,
                'ruta'   => $archivo->getRealPath(),
                'tamano' => $archivo->getSize(),
                'fecha'  => Carbon::createFromTimestamp($archivo->getMTime()),
                'tipo'   => $tipo,
            ];
        }

        usort($copias, fn($a, $b) => $b['fecha']->timestamp <=> $a['fecha']->timestamp);

        return $copias;
    }
}
