<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivoFijo;
use App\Models\Ambiente;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    $activo = ActivoFijo::with(['categoria', 'ambiente.carrera', 'fuente', 'custodio'])
        ->findOrFail($activo_id);

    $pdf = Pdf::loadView('actas.alta.pdf', compact('activo'))
        ->setPaper([0, 0, 612, 936], 'portrait') // OFICIO
        ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    return $pdf->stream('Acta_Alta_' . $activo->codigo_activo . '.pdf');
}
// ============================================================
// PDF — ACTA DE RECEPCIÓN INDIVIDUAL (un solo activo)
// ============================================================
public function actaRecepcionActivoPdf($activo_id)
{
    // Cargar el activo con todas sus relaciones
    $activo = ActivoFijo::with([
        'categoria',
        'ambiente.carrera',
        'fuente',
        'custodio',
    ])->findOrFail($activo_id);

    // Construir una colección con un solo activo para reutilizar la vista
    $activos = collect([$activo]);

    // Firmante del acta
    $responsable = Auth::user();

    // Generar PDF en OFICIO
    $pdf = Pdf::loadView('actas.recepcion.pdf_individual', compact('activo', 'activos', 'responsable'))
        ->setPaper([0, 0, 612, 936], 'portrait') // OFICIO
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
    // Cargar el activo con todas sus relaciones
    $activo = ActivoFijo::with([
        'categoria',
        'ambiente.carrera',
        'fuente',
        'custodio',
    ])->findOrFail($activo_id);

    // Construir una colección con un solo activo
    $activos = collect([$activo]);

    // Firmante del acta
    $responsable = Auth::user();

    // Retornar el Excel
    return response()
        ->view('actas.recepcion.excel_individual', compact('activo', 'activos', 'responsable'))
        ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="Acta_Recepcion_' . $activo->codigo_activo . '.xls"');
}
}
