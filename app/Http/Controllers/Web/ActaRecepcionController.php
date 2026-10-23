<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivoFijo;
use App\Models\Ambiente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActaRecepcionController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================
    public function index()
    {
        return view('actas.recepcion.index', [
            'ambientes' => Ambiente::with('carrera')->orderBy('nombre')->get(),
        ]);
    }

    // ============================================================
    // PDF — Acta de recepción por ambiente
    // ============================================================
    public function pdf(Request $request, $ambiente_id = null)
    {
        if (! $ambiente_id) {
            $ambiente_id = $request->query('ambiente_id');
        }

        abort_if(! $ambiente_id, 400, 'Falta el parámetro ambiente_id');

        $ambiente = Ambiente::with('carrera')->findOrFail($ambiente_id);

        $activos = ActivoFijo::where('id_ambiente', $ambiente_id)
            ->with(['categoria', 'fuente', 'custodio'])
            ->orderBy('nombre')
            ->get();

        $responsable = Auth::user();

        $pdf = Pdf::loadView('actas.recepcion.pdf', compact('ambiente', 'activos', 'responsable'))
            ->setPaper([0, 0, 612, 936], 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream('Acta_Recepcion_' . str_replace(' ', '_', $ambiente->nombre) . '.pdf');
    }

    // ============================================================
    // PDF — Acta de ALTA (ingreso de nuevo activo)
    // ============================================================
    public function actaAltaPdf($activo_id)
    {
        $activo = ActivoFijo::with([
            'categoria',
            'ambiente.carrera',
            'fuente',
            'custodio',
            'item'
        ])->findOrFail($activo_id);

        $responsables = collect();
        $ambienteId = $activo->id_ambiente ?? null;
        if ($activo->item && ! $ambienteId) {
            $ambienteId = $activo->item->id_ambiente ?? null;
        }

        if ($ambienteId) {
            $rows = DB::table('ambiente_user')
                ->join('usuarios', 'usuarios.id_usuario', '=', 'ambiente_user.id_usuario')
                ->where('ambiente_user.id_ambiente', $ambienteId)
                ->where('usuarios.estado', 'ACTIVO')
                ->orderBy('usuarios.nombre_completo')
                ->select('usuarios.id_usuario', 'usuarios.nombre_completo', 'usuarios.ci', 'usuarios.cargo', 'usuarios.rol', 'usuarios.estado')
                ->distinct()
                ->get();
            foreach ($rows as $row) {
                $u = new User((array) $row);
                $u->exists = true;
                $responsables->push($u);
            }
        }

        if ($responsables->isEmpty() && $activo->id_item) {
            $rows = DB::table('item_usuario')
                ->join('usuarios', 'usuarios.id_usuario', '=', 'item_usuario.id_usuario')
                ->where('item_usuario.id_item', $activo->id_item)
                ->whereNull('item_usuario.fecha_fin')
                ->where('usuarios.estado', 'ACTIVO')
                ->select('usuarios.id_usuario', 'usuarios.nombre_completo', 'usuarios.ci', 'usuarios.cargo', 'usuarios.rol', 'usuarios.estado', 'item_usuario.tipo')
                ->orderByRaw("FIELD(item_usuario.tipo, 'TITULAR', 'RESPONSABLE', 'SECUNDARIO')")
                ->orderBy('usuarios.nombre_completo')
                ->get();
            foreach ($rows as $row) {
                $u = new User((array) $row);
                $u->exists = true;
                $responsables->push($u);
            }
        }

        if ($responsables->isEmpty() && $activo->custodio) {
            $responsables = collect([$activo->custodio]);
        }

        $pdf = Pdf::loadView('actas.alta.pdf', compact('activo', 'responsables'))
            ->setPaper('legal', 'landscape')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'isFontSubsetting' => true,
                'dpi' => 96,
            ]);

        return $pdf->stream('Acta_Alta_' . $activo->codigo_activo . '.pdf');
    }

    // ============================================================
    // PDF — ACTA DE RECEPCIÓN INDIVIDUAL (un solo activo)
    // ============================================================
    public function actaRecepcionActivoPdf($activo_id)
    {
        $activo = ActivoFijo::with([
            'categoria',
            'ambiente.carrera',
            'fuente',
            'custodio',
        ])->findOrFail($activo_id);

        $activos = collect([$activo]);
        $responsable = Auth::user();

        $pdf = Pdf::loadView('actas.recepcion.pdf_individual', compact('activo', 'activos', 'responsable'))
            ->setPaper([0, 0, 612, 936], 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ]);

        return $pdf->stream('Acta_Recepcion_' . $activo->codigo_activo . '.pdf');
    }

    // ============================================================
    // EXCEL — ACTA DE RECEPCIÓN INDIVIDUAL (un solo activo)
    // ============================================================
    public function actaRecepcionActivoExcel($activo_id)
    {
        $activo = ActivoFijo::with([
            'categoria',
            'ambiente.carrera',
            'fuente',
            'custodio',
        ])->findOrFail($activo_id);

        $activos = collect([$activo]);
        $responsable = Auth::user();

        return response()
            ->view('actas.recepcion.excel_individual', compact('activo', 'activos', 'responsable'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="Acta_Recepcion_' . $activo->codigo_activo . '.xls"');
    }
}
