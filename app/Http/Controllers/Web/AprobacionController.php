<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActaBaja;
use App\Models\ActaTransferencia;
use App\Models\VerificacionFisica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AprobacionController extends Controller
{
    /**
     * Bandeja de la administradora: todo lo que el inventario pidió y
     * todavía no está resuelto, en un solo lugar.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->puedeAprobar(), 403, 'Solo la administración revisa aprobaciones.');

        $tipo = $request->get('tipo', 'TODOS');

        $verificaciones = $tipo === 'bajas' || $tipo === 'transferencias'
            ? collect()
            : VerificacionFisica::with(['activo.ambiente', 'verificador'])
                ->pendientesAprobacion()
                ->latest('fecha_verificacion')
                ->limit(50)
                ->get();

        $bajas = $tipo === 'verificaciones' || $tipo === 'transferencias'
            ? collect()
            : ActaBaja::with(['activo.ambiente', 'usuario'])
                ->pendientes()
                ->latest('id_acta_baja')
                ->limit(50)
                ->get();

        $transferencias = $tipo === 'verificaciones' || $tipo === 'bajas'
            ? collect()
            : ActaTransferencia::with(['activo', 'origenAmbiente', 'destinoAmbiente', 'custodioAnterior'])
                ->pendientes()
                ->latest('id_acta_transferencia')
                ->get();

        $totales = [
            'verificaciones' => VerificacionFisica::pendientesAprobacion()->count(),
            'bajas'          => ActaBaja::pendientes()->count(),
            'transferencias' => ActaTransferencia::pendientes()->count(),
        ];

        return view('aprobaciones.index', compact(
            'verificaciones',
            'bajas',
            'transferencias',
            'totales',
            'tipo'
        ));
    }
}