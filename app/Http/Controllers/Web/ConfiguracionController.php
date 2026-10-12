<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    public function index()
    {
        // Verificar si la tabla existe antes de hacer la consulta
        $config = null;
        if (Schema::hasTable('configuraciones')) {
            $config = DB::table('configuraciones')->first();
        }

        // Si no existe la tabla o no hay registros, enviar un objeto por defecto
        if (!$config) {
            $config = (object)[
                'institucion_nombre' => 'Instituto Tecnológico "Puerto de Mejillones"',
                'institucion_direccion' => 'El Alto, La Paz - Bolivia',
                'institucion_director' => 'Lic. Jimmy Ovidio Sirpa Choque',
                'gestion_fiscal' => '2026',
                'prefijo_activo' => 'TPM-',
                'validacion_estricta' => 1
            ];
        }

        return view('configuracion.index', compact('config'));
    }
    public function update(Request $request)
{
    if (! Schema::hasTable('configuraciones')) {
        return back()->with('error', 'La tabla configuraciones no existe.');
    }

    $data = $request->validate([
        'institucion_nombre'    => 'required|string|max:255',
        'institucion_direccion' => 'nullable|string|max:255',
        'institucion_director'  => 'nullable|string|max:255',
    ]);

    // ⭐ Cambiado: id_configuracion en lugar de id
    DB::table('configuraciones')->updateOrInsert(['id_configuracion' => 1], [
        'institucion_nombre'    => $data['institucion_nombre'],
        'institucion_direccion' => $data['institucion_direccion'],
        'institucion_director'  => $data['institucion_director'],
        'updated_at'            => now(),
    ]);

    return back()->with('success', 'Datos institucionales actualizados.');
}

public function updateParametros(Request $request)
{
    if (! Schema::hasTable('configuraciones')) {
        return back()->with('error', 'La tabla configuraciones no existe.');
    }

    $data = $request->validate([
        'gestion_fiscal'      => 'required|string|max:10',
        'prefijo_activo'      => 'required|string|max:20',
        'validacion_estricta' => 'nullable|boolean',
    ]);

    // ⭐ Cambiado: id_configuracion
    DB::table('configuraciones')->updateOrInsert(['id_configuracion' => 1], [
        'gestion_fiscal'      => $data['gestion_fiscal'],
        'prefijo_activo'      => $data['prefijo_activo'],
        'validacion_estricta' => $request->has('validacion_estricta') ? 1 : 0,
        'updated_at'          => now(),
    ]);

    return back()->with('success', 'Parámetros actualizados correctamente.');
}


}
