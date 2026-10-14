<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivoFijo;
use App\Models\Ambiente;
use App\Models\Carrera;
use App\Models\Categoria;
use App\Models\FuenteFinanciamiento;
use App\Support\PdfHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    // ============================================================
    // PANEL PRINCIPAL DE REPORTES
    // ============================================================
    public function index(Request $request)
    {
        $user = Auth::user();

        // Totales generales excluyendo los dados de baja
        $queryBase = ActivoFijo::paraUsuario($user)->where("estado_registro", "!=", "BAJA");

        $totalBuenos    = (clone $queryBase)->where("estado_fisico", "B")->count();
        $totalRegulares = (clone $queryBase)->where("estado_fisico", "R")->count();
        $totalMalos     = (clone $queryBase)->where("estado_fisico", "M")->count();
        $totalFueraServ = (clone $queryBase)->where("estado_fisico", "FF")->count();

        $carreras   = Carrera::when(!$user || !($user->esAdmin() || $user->esOperativo()), function ($q) use ($user) {
            $ids = $user?->idsCarrerasVisibles();
            if ($ids === null) return $q;
            return $q->whereIn("id_carrera", $ids);
        })->orderBy("nombre")->get();

        $ambientes  = Ambiente::with("carrera")->when(!$user || !($user->esAdmin() || $user->esOperativo()), function ($q) use ($user) {
            $ids = $user?->idsAmbientesVisibles();
            if (empty($ids)) return $q->whereRaw("1=0");
            return $q->whereIn("id_ambiente", $ids);
        })->orderBy("nombre")->get();

        $categorias = Categoria::orderBy("nombre")->get();

        return view("reportes.index", compact(
            "totalBuenos", "totalRegulares", "totalMalos", "totalFueraServ",
            "carreras", "ambientes", "categorias"
        ));
    }

    // ============================================================
    // 1. INVENTARIO GENERAL
    // ============================================================
    public function inventarioGeneral(Request $request)
    {
        $tipoBien = $this->tipoBienReporte($request);

        $query = ActivoFijo::query()
            ->selectRaw('
                GROUP_CONCAT(activos_fijos.codigo_activo ORDER BY activos_fijos.codigo_activo SEPARATOR " ") as codigos,
                GROUP_CONCAT(activos_fijos.id_activo ORDER BY activos_fijos.id_activo SEPARATOR ",") as ids_array,
                activos_fijos.nombre as descripcion,
                CONCAT_WS(", ",
                    activos_fijos.marca,
                    activos_fijos.modelo,
                    GROUP_CONCAT(DISTINCT activos_fijos.numero_serie ORDER BY activos_fijos.numero_serie SEPARATOR ", "),
                    activos_fijos.descripcion
                ) as caracteristicas,
                COUNT(*) as cantidad,
                SUM(activos_fijos.valor_adquisicion) as costo_total,
                activos_fijos.estado_fisico,
                fuentes_financiamiento.nombre as fuente,
                fuentes_financiamiento.codigo as fuente_codigo,
                MIN(activos_fijos.fecha_adquisicion) as fecha_entrega,
                carreras.nombre as carrera,
                activos_fijos.observaciones,
                activos_fijos.imagen
            ')
            ->join('categorias', 'activos_fijos.id_categoria', '=', 'categorias.id_categoria')
            ->join('ambientes', 'activos_fijos.id_ambiente', '=', 'ambientes.id_ambiente')
            ->join('carreras', 'ambientes.id_carrera', '=', 'carreras.id_carrera')
            ->leftJoin('fuentes_financiamiento', 'activos_fijos.id_fuente', '=', 'fuentes_financiamiento.id_fuente')
            ->where('activos_fijos.estado_registro', '!=', 'BAJA')
            ->tipoBien($tipoBien);

        if ($request->filled('carrera')) {
            $query->where('carreras.id_carrera', $request->carrera);
        }
        if ($request->filled('ambiente')) {
            $query->where('activos_fijos.id_ambiente', $request->ambiente);
        }
        if ($request->filled('estado_fisico')) {
            $query->where('activos_fijos.estado_fisico', $request->estado_fisico);
        }
        if ($request->filled('fuente')) {
            $query->where('activos_fijos.id_fuente', $request->fuente);
        }
        if ($request->filled('anio')) {
            $query->whereYear('activos_fijos.fecha_adquisicion', $request->anio);
        }

        $query->groupBy(
            'activos_fijos.nombre',
            'activos_fijos.marca',
            'activos_fijos.modelo',
            'activos_fijos.descripcion',
            'activos_fijos.estado_fisico',
            'fuentes_financiamiento.nombre',
            'carreras.nombre',
            'activos_fijos.observaciones',
            'activos_fijos.imagen'
        )
        ->orderBy('activos_fijos.modelo')
        ->orderBy('activos_fijos.marca')
        ->orderBy('activos_fijos.nombre');

        $reportes = $query->get()->map(function ($item, $index) {
            $fecha = $item->fecha_entrega;
            if (is_string($fecha)) {
                $fecha = Carbon::parse($fecha);
            }
            return (object) [
                'nro' => $index + 1,
                'codigos' => $item->codigos,
                'ids_array' => explode(',', $item->ids_array),
                'descripcion' => $item->descripcion,
                'caracteristicas' => $item->caracteristicas,
                'cantidad' => $item->cantidad,
                'costo_total' => $item->costo_total,
                'estado_fisico' => $item->estado_fisico,
                'estado_fisico_texto' => $this->estadoFisicoTexto($item->estado_fisico),
                'fuente' => $item->fuente,
                'fuente_codigo' => $item->fuente_codigo,
                'fecha_entrega' => $fecha,
                'dia' => $fecha ? $fecha->format('d') : '',
                'mes' => $fecha ? $fecha->format('m') : '',
                'anio' => $fecha ? $fecha->format('Y') : '',
                'carrera' => $item->carrera,
                'observaciones' => $item->observaciones,
                'imagen' => $item->imagen,
            ];
        });

        $carreras = Carrera::orderBy('nombre')->get();
        $ambientes = Ambiente::orderBy('nombre')->get();
        $fuentes = FuenteFinanciamiento::orderBy('nombre')->get();

        return view('reportes.inventario_general', compact('reportes', 'carreras', 'ambientes', 'fuentes', 'tipoBien'));
    }

    public function inventarioGeneralPdf(Request $request)
    {
        $tipoBien = $this->tipoBienReporte($request);

        $query = ActivoFijo::query()
            ->selectRaw('
                GROUP_CONCAT(activos_fijos.codigo_activo ORDER BY activos_fijos.codigo_activo SEPARATOR " ") as codigos,
                activos_fijos.nombre as descripcion,
                CONCAT_WS(", ",
                    activos_fijos.marca,
                    activos_fijos.modelo,
                    GROUP_CONCAT(DISTINCT activos_fijos.numero_serie ORDER BY activos_fijos.numero_serie SEPARATOR ", "),
                    activos_fijos.descripcion
                ) as caracteristicas,
                COUNT(*) as cantidad,
                SUM(activos_fijos.valor_adquisicion) as costo_total,
                activos_fijos.estado_fisico,
                fuentes_financiamiento.nombre as fuente,
                fuentes_financiamiento.codigo as fuente_codigo,
                MIN(activos_fijos.fecha_adquisicion) as fecha_entrega,
                carreras.nombre as carrera,
                activos_fijos.observaciones,
                activos_fijos.imagen
            ')
            ->join('categorias', 'activos_fijos.id_categoria', '=', 'categorias.id_categoria')
            ->join('ambientes', 'activos_fijos.id_ambiente', '=', 'ambientes.id_ambiente')
            ->join('carreras', 'ambientes.id_carrera', '=', 'carreras.id_carrera')
            ->leftJoin('fuentes_financiamiento', 'activos_fijos.id_fuente', '=', 'fuentes_financiamiento.id_fuente')
            ->where('activos_fijos.estado_registro', '!=', 'BAJA')
            ->tipoBien($tipoBien);

        if ($request->filled('carrera')) {
            $query->where('carreras.id_carrera', $request->carrera);
        }
        if ($request->filled('ambiente')) {
            $query->where('activos_fijos.id_ambiente', $request->ambiente);
        }
        if ($request->filled('estado_fisico')) {
            $query->where('activos_fijos.estado_fisico', $request->estado_fisico);
        }
        if ($request->filled('fuente')) {
            $query->where('activos_fijos.id_fuente', $request->fuente);
        }
        if ($request->filled('anio')) {
            $query->whereYear('activos_fijos.fecha_adquisicion', $request->anio);
        }

        $query->groupBy(
            'activos_fijos.nombre',
            'activos_fijos.marca',
            'activos_fijos.modelo',
            'activos_fijos.descripcion',
            'activos_fijos.estado_fisico',
            'fuentes_financiamiento.nombre',
            'carreras.nombre',
            'activos_fijos.observaciones',
            'activos_fijos.imagen'
        )
        ->orderBy('activos_fijos.modelo')
        ->orderBy('activos_fijos.marca')
        ->orderBy('activos_fijos.nombre');

        $reportes = $query->get()->map(function ($item, $index) {
            $fecha = $item->fecha_entrega;
            if (is_string($fecha)) {
                $fecha = Carbon::parse($fecha);
            }
            return (object) [
                'nro' => $index + 1,
                'codigos' => $item->codigos,
                'ids_array' => [],
                'descripcion' => $item->descripcion,
                'caracteristicas' => $item->caracteristicas,
                'cantidad' => $item->cantidad,
                'costo_total' => $item->costo_total,
                'estado_fisico' => $item->estado_fisico,
                'estado_fisico_texto' => $this->estadoFisicoTexto($item->estado_fisico),
                'fuente' => $item->fuente,
                'fuente_codigo' => $item->fuente_codigo,
                'fecha_entrega' => $fecha,
                'dia' => $fecha ? $fecha->format('d') : '',
                'mes' => $fecha ? $fecha->format('m') : '',
                'anio' => $fecha ? $fecha->format('Y') : '',
                'carrera' => $item->carrera,
                'observaciones' => $item->observaciones,
                'imagen' => $item->imagen,
            ];
        });

        $pdf = PdfHelper::oficio('reportes.pdf.inventario_general', compact('reportes', 'tipoBien'), 'landscape');
        return $pdf->stream('Inventario_' . $this->sufijoTipoBien($tipoBien) . 'General_' . date('Y-m-d') . '.pdf');
    }

    public function inventarioGeneralExcel(Request $request)
    {
        $tipoBien = $this->tipoBienReporte($request);

        $query = ActivoFijo::query()
            ->selectRaw('
                GROUP_CONCAT(activos_fijos.codigo_activo ORDER BY activos_fijos.codigo_activo SEPARATOR " ") as codigos,
                activos_fijos.nombre as descripcion,
                CONCAT_WS(", ",
                    activos_fijos.marca,
                    activos_fijos.modelo,
                    GROUP_CONCAT(DISTINCT activos_fijos.numero_serie ORDER BY activos_fijos.numero_serie SEPARATOR ", "),
                    activos_fijos.descripcion
                ) as caracteristicas,
                COUNT(*) as cantidad,
                SUM(activos_fijos.valor_adquisicion) as costo_total,
                activos_fijos.estado_fisico,
                fuentes_financiamiento.nombre as fuente,
                fuentes_financiamiento.codigo as fuente_codigo,
                MIN(activos_fijos.fecha_adquisicion) as fecha_entrega,
                carreras.nombre as carrera,
                activos_fijos.observaciones,
                activos_fijos.imagen
            ')
            ->join('categorias', 'activos_fijos.id_categoria', '=', 'categorias.id_categoria')
            ->join('ambientes', 'activos_fijos.id_ambiente', '=', 'ambientes.id_ambiente')
            ->join('carreras', 'ambientes.id_carrera', '=', 'carreras.id_carrera')
            ->leftJoin('fuentes_financiamiento', 'activos_fijos.id_fuente', '=', 'fuentes_financiamiento.id_fuente')
            ->where('activos_fijos.estado_registro', '!=', 'BAJA')
            ->tipoBien($tipoBien);

        if ($request->filled('carrera')) {
            $query->where('carreras.id_carrera', $request->carrera);
        }
        if ($request->filled('ambiente')) {
            $query->where('activos_fijos.id_ambiente', $request->ambiente);
        }
        if ($request->filled('estado_fisico')) {
            $query->where('activos_fijos.estado_fisico', $request->estado_fisico);
        }
        if ($request->filled('fuente')) {
            $query->where('activos_fijos.id_fuente', $request->fuente);
        }
        if ($request->filled('anio')) {
            $query->whereYear('activos_fijos.fecha_adquisicion', $request->anio);
        }

        $query->groupBy(
            'activos_fijos.nombre',
            'activos_fijos.marca',
            'activos_fijos.modelo',
            'activos_fijos.descripcion',
            'activos_fijos.estado_fisico',
            'fuentes_financiamiento.nombre',
            'carreras.nombre',
            'activos_fijos.observaciones',
            'activos_fijos.imagen'
        )
        ->orderBy('activos_fijos.modelo')
        ->orderBy('activos_fijos.marca')
        ->orderBy('activos_fijos.nombre');

        $reportes = $query->get()->map(function ($item, $index) {
            $fecha = $item->fecha_entrega;
            if (is_string($fecha)) {
                $fecha = Carbon::parse($fecha);
            }
            return (object) [
                'nro' => $index + 1,
                'codigos' => $item->codigos,
                'ids_array' => [],
                'descripcion' => $item->descripcion,
                'caracteristicas' => $item->caracteristicas,
                'cantidad' => $item->cantidad,
                'costo_total' => $item->costo_total,
                'estado_fisico' => $item->estado_fisico,
                'estado_fisico_texto' => $this->estadoFisicoTexto($item->estado_fisico),
                'fuente' => $item->fuente,
                'fuente_codigo' => $item->fuente_codigo,
                'fecha_entrega' => $fecha,
                'dia' => $fecha ? $fecha->format('d') : '',
                'mes' => $fecha ? $fecha->format('m') : '',
                'anio' => $fecha ? $fecha->format('Y') : '',
                'carrera' => $item->carrera,
                'observaciones' => $item->observaciones,
                'imagen' => $item->imagen,
            ];
        });

        return response()
            ->view('reportes.excel.inventario_general', compact('reportes', 'tipoBien'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="Inventario_'
                . $this->sufijoTipoBien($tipoBien) . 'General_' . date('Y-m-d') . '.xls"');
    }

    private function tipoBienReporte(Request $request): string
    {
        $tipo = $request->input('tipo_bien', 'TODOS');
        return in_array($tipo, ['ACTIVO_FIJO', 'NO_ACTIVO'], true) ? $tipo : 'TODOS';
    }

    private function sufijoTipoBien(string $tipo): string
    {
        return match ($tipo) {
            'ACTIVO_FIJO' => 'Activos_Fijos_',
            'NO_ACTIVO'   => 'No_Activos_',
            default       => 'Todos_',
        };
    }

    // ============================================================
    // 2. INVENTARIO POR AMBIENTE
    // ============================================================
    public function porAmbiente($id_ambiente)
    {
        $ambiente = $this->ambienteVisible($id_ambiente);

        $query = ActivoFijo::query()
            ->selectRaw('
                GROUP_CONCAT(activos_fijos.codigo_activo ORDER BY activos_fijos.codigo_activo SEPARATOR " ") as codigos,
                activos_fijos.nombre as descripcion,
                CONCAT_WS(", ",
                    activos_fijos.marca,
                    activos_fijos.modelo,
                    GROUP_CONCAT(DISTINCT activos_fijos.numero_serie ORDER BY activos_fijos.numero_serie SEPARATOR ", "),
                    activos_fijos.descripcion
                ) as caracteristicas,
                COUNT(*) as cantidad,
                SUM(activos_fijos.valor_adquisicion) as costo_total,
                activos_fijos.estado_fisico,
                fuentes_financiamiento.nombre as fuente,
                MIN(activos_fijos.fecha_adquisicion) as fecha_entrega,
                carreras.nombre as carrera,
                activos_fijos.observaciones,
                activos_fijos.imagen
            ')
            ->join('categorias', 'activos_fijos.id_categoria', '=', 'categorias.id_categoria')
            ->join('ambientes', 'activos_fijos.id_ambiente', '=', 'ambientes.id_ambiente')
            ->join('carreras', 'ambientes.id_carrera', '=', 'carreras.id_carrera')
            ->leftJoin('fuentes_financiamiento', 'activos_fijos.id_fuente', '=', 'fuentes_financiamiento.id_fuente')
            ->where('activos_fijos.estado_registro', '!=', 'BAJA')
            ->where('activos_fijos.id_ambiente', $id_ambiente)
            ->groupBy(
                'activos_fijos.nombre',
                'activos_fijos.marca',
                'activos_fijos.modelo',
                'activos_fijos.descripcion',
                'activos_fijos.estado_fisico',
                'fuentes_financiamiento.nombre',
                'carreras.nombre',
                'activos_fijos.observaciones',
                'activos_fijos.imagen'
            )
            ->orderBy('activos_fijos.modelo')
            ->orderBy('activos_fijos.marca')
            ->orderBy('activos_fijos.nombre');

        $reportes = $query->get()->map(function ($item, $index) {
            $fecha = $item->fecha_entrega;
            if (is_string($fecha)) {
                $fecha = Carbon::parse($fecha);
            }
            return (object) [
                'nro' => $index + 1,
                'codigos' => $item->codigos,
                'ids_array' => [],
                'descripcion' => $item->descripcion,
                'caracteristicas' => $item->caracteristicas,
                'cantidad' => $item->cantidad,
                'costo_total' => $item->costo_total,
                'estado_fisico' => $item->estado_fisico,
                'estado_fisico_texto' => $this->estadoFisicoTexto($item->estado_fisico),
                'fuente' => $item->fuente,
                'fuente_codigo' => $item->fuente_codigo ?? '',
                'fecha_entrega' => $fecha,
                'dia' => $fecha ? $fecha->format('d') : '',
                'mes' => $fecha ? $fecha->format('m') : '',
                'anio' => $fecha ? $fecha->format('Y') : '',
                'carrera' => $item->carrera,
                'observaciones' => $item->observaciones,
                'imagen' => $item->imagen,
            ];
        });

        return view('reportes.por_ambiente', compact('ambiente', 'reportes'));
    }

    public function porAmbientePdf($id_ambiente)
    {
        $ambiente = $this->ambienteVisible($id_ambiente);
        // Reutiliza la misma lÃ³gica de consulta por ambiente
        return $this->porAmbiente($id_ambiente); // o genera PDF directamente
    }

    public function porAmbienteExcel($id_ambiente)
    {
        $ambiente = $this->ambienteVisible($id_ambiente);
        return response()
            ->view('reportes.excel.por_ambiente', compact('ambiente'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
    }

    // ============================================================
    // 3. MÃ‰TODOS DINÃMICOS PARA REPORTES (DESDE EL CENTRO DE REPORTES)
    // ============================================================
    public function pdf(Request $request)
    {
        $tipoBien = $this->tipoBienReporte($request);

        $query = ActivoFijo::query()
            ->selectRaw('
                GROUP_CONCAT(activos_fijos.codigo_activo ORDER BY activos_fijos.codigo_activo SEPARATOR " ") as codigos,
                activos_fijos.nombre as descripcion,
                CONCAT_WS(", ",
                    activos_fijos.marca,
                    activos_fijos.modelo,
                    GROUP_CONCAT(DISTINCT activos_fijos.numero_serie ORDER BY activos_fijos.numero_serie SEPARATOR ", "),
                    activos_fijos.descripcion
                ) as caracteristicas,
                COUNT(*) as cantidad,
                SUM(activos_fijos.valor_adquisicion) as costo_total,
                activos_fijos.estado_fisico,
                fuentes_financiamiento.nombre as fuente,
                fuentes_financiamiento.codigo as fuente_codigo,
                MIN(activos_fijos.fecha_adquisicion) as fecha_entrega,
                carreras.nombre as carrera,
                activos_fijos.observaciones,
                activos_fijos.imagen
            ')
            ->join('categorias', 'activos_fijos.id_categoria', '=', 'categorias.id_categoria')
            ->join('ambientes', 'activos_fijos.id_ambiente', '=', 'ambientes.id_ambiente')
            ->join('carreras', 'ambientes.id_carrera', '=', 'carreras.id_carrera')
            ->leftJoin('fuentes_financiamiento', 'activos_fijos.id_fuente', '=', 'fuentes_financiamiento.id_fuente')
            ->where('activos_fijos.estado_registro', '!=', 'BAJA')
            ->tipoBien($tipoBien);

        if ($request->filled('id_ambiente')) {
            $query->where('activos_fijos.id_ambiente', $request->id_ambiente);
        }
        if ($request->filled('id_carrera')) {
            $query->where('carreras.id_carrera', $request->id_carrera);
        }
        if ($request->filled('id_categoria')) {
            $query->where('activos_fijos.id_categoria', $request->id_categoria);
        }

        $query->groupBy(
            'activos_fijos.nombre',
            'activos_fijos.marca',
            'activos_fijos.modelo',
            'activos_fijos.descripcion',
            'activos_fijos.estado_fisico',
            'fuentes_financiamiento.nombre',
            'carreras.nombre',
            'activos_fijos.observaciones',
            'activos_fijos.imagen'
        )
        ->orderBy('activos_fijos.modelo')
        ->orderBy('activos_fijos.marca')
        ->orderBy('activos_fijos.nombre');

        $reportes = $query->get()->map(function ($item, $index) {
            $fecha = $item->fecha_entrega;
            if (is_string($fecha)) {
                $fecha = Carbon::parse($fecha);
            }
            return (object) [
                'nro' => $index + 1,
                'codigos' => $item->codigos,
                'ids_array' => [],
                'descripcion' => $item->descripcion,
                'caracteristicas' => $item->caracteristicas,
                'cantidad' => $item->cantidad,
                'costo_total' => $item->costo_total,
                'estado_fisico' => $item->estado_fisico,
                'estado_fisico_texto' => $this->estadoFisicoTexto($item->estado_fisico),
                'fuente' => $item->fuente,
                'fuente_codigo' => $item->fuente_codigo ?? '',
                'fecha_entrega' => $fecha,
                'dia' => $fecha ? $fecha->format('d') : '',
                'mes' => $fecha ? $fecha->format('m') : '',
                'anio' => $fecha ? $fecha->format('Y') : '',
                'carrera' => $item->carrera,
                'observaciones' => $item->observaciones,
                'imagen' => $item->imagen,
            ];
        });

        $pdf = PdfHelper::oficio('reportes.pdf.inventario_general', compact('reportes', 'tipoBien'), 'landscape');
        return $pdf->stream('Inventario_Filtro_' . date('Y-m-d') . '.pdf');
    }

    public function excel(Request $request)
    {
        $tipoBien = $this->tipoBienReporte($request);

        $query = ActivoFijo::query()
            ->selectRaw('
                GROUP_CONCAT(activos_fijos.codigo_activo ORDER BY activos_fijos.codigo_activo SEPARATOR " ") as codigos,
                activos_fijos.nombre as descripcion,
                CONCAT_WS(", ",
                    activos_fijos.marca,
                    activos_fijos.modelo,
                    GROUP_CONCAT(DISTINCT activos_fijos.numero_serie ORDER BY activos_fijos.numero_serie SEPARATOR ", "),
                    activos_fijos.descripcion
                ) as caracteristicas,
                COUNT(*) as cantidad,
                SUM(activos_fijos.valor_adquisicion) as costo_total,
                activos_fijos.estado_fisico,
                fuentes_financiamiento.nombre as fuente,
                fuentes_financiamiento.codigo as fuente_codigo,
                MIN(activos_fijos.fecha_adquisicion) as fecha_entrega,
                carreras.nombre as carrera,
                activos_fijos.observaciones,
                activos_fijos.imagen
            ')
            ->join('categorias', 'activos_fijos.id_categoria', '=', 'categorias.id_categoria')
            ->join('ambientes', 'activos_fijos.id_ambiente', '=', 'ambientes.id_ambiente')
            ->join('carreras', 'ambientes.id_carrera', '=', 'carreras.id_carrera')
            ->leftJoin('fuentes_financiamiento', 'activos_fijos.id_fuente', '=', 'fuentes_financiamiento.id_fuente')
            ->where('activos_fijos.estado_registro', '!=', 'BAJA')
            ->tipoBien($tipoBien);

        if ($request->filled('id_ambiente')) {
            $query->where('activos_fijos.id_ambiente', $request->id_ambiente);
        }
        if ($request->filled('id_carrera')) {
            $query->where('carreras.id_carrera', $request->id_carrera);
        }
        if ($request->filled('id_categoria')) {
            $query->where('activos_fijos.id_categoria', $request->id_categoria);
        }

        $query->groupBy(
            'activos_fijos.nombre',
            'activos_fijos.marca',
            'activos_fijos.modelo',
            'activos_fijos.descripcion',
            'activos_fijos.estado_fisico',
            'fuentes_financiamiento.nombre',
            'carreras.nombre',
            'activos_fijos.observaciones',
            'activos_fijos.imagen'
        )
        ->orderBy('activos_fijos.modelo')
        ->orderBy('activos_fijos.marca')
        ->orderBy('activos_fijos.nombre');

        $reportes = $query->get()->map(function ($item, $index) {
            $fecha = $item->fecha_entrega;
            if (is_string($fecha)) {
                $fecha = Carbon::parse($fecha);
            }
            return (object) [
                'nro' => $index + 1,
                'codigos' => $item->codigos,
                'ids_array' => [],
                'descripcion' => $item->descripcion,
                'caracteristicas' => $item->caracteristicas,
                'cantidad' => $item->cantidad,
                'costo_total' => $item->costo_total,
                'estado_fisico' => $item->estado_fisico,
                'estado_fisico_texto' => $this->estadoFisicoTexto($item->estado_fisico),
                'fuente' => $item->fuente,
                'fuente_codigo' => $item->fuente_codigo ?? '',
                'fecha_entrega' => $fecha,
                'dia' => $fecha ? $fecha->format('d') : '',
                'mes' => $fecha ? $fecha->format('m') : '',
                'anio' => $fecha ? $fecha->format('Y') : '',
                'carrera' => $item->carrera,
                'observaciones' => $item->observaciones,
                'imagen' => $item->imagen,
            ];
        });

        return response()
            ->view('reportes.excel.inventario_general', compact('reportes', 'tipoBien'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="Inventario_Filtro_' . date('Y-m-d') . '.xls"');
    }

    // ============================================================
    // HELPERS Y ALCANCE
    // ============================================================
    protected function ambienteVisible($id): Ambiente
    {
        return Auth::user()->ambientesVisibles()->with('carrera')->findOrFail($id);
    }

    protected function estadoFisicoTexto($estado): string
    {
        return match ($estado) {
            'B' => 'BUENO',
            'R' => 'REGULAR',
            'M' => 'MALO',
            'FF' => 'FUERA DE FUNCIONAMIENTO',
            default => 'DESCONOCIDO',
        };
    }
}
