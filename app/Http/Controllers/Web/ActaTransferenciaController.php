<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActaTransferencia;
use App\Models\ActivoFijo;
use App\Models\Ambiente;
use App\Models\User;
use App\Support\PdfHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActaTransferenciaController extends Controller
{
    // ============================================================
    // INDEX — Listado de transferencias
    // ============================================================
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ActaTransferencia::with([
            'activo',
            'origenAmbiente',
            'destinoAmbiente',
            'custodioAnterior',
            'custodioNuevo',
            'aprobador',
        ])->latest();

        if (! $user->esAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('id_custodio_anterior', $user->id_usuario)
                  ->orWhere('id_custodio_nuevo', $user->id_usuario);
            });
        }

        if ($request->filled('estado') && in_array($request->estado, [
            ActaTransferencia::ESTADO_PENDIENTE,
            ActaTransferencia::ESTADO_APROBADA,
            ActaTransferencia::ESTADO_RECHAZADA,
        ], true)) {
            $query->where('estado', $request->estado);
        }

        $transferencias = $query->paginate(15)->appends($request->query());

        $ambientesVisibles = $user->ambientesVisibles()->with('carrera')->orderBy('nombre')->get();

        return view('actas.transferencia.index', [
            'transferencias' => $transferencias,
            'activos'        => ActivoFijo::paraUsuario($user)->where('estado_registro', '!=', 'BAJA')->get(),
            'ambientes'      => $ambientesVisibles,
            'usuarios'       => User::where('estado', 'ACTIVO')->orderBy('nombre_completo')->get(),
            'pendientes'     => ActaTransferencia::pendientes()->count(),
        ]);
    }

    // ============================================================
    // CREATE — Formulario de nueva transferencia (CORREGIDO)
    // ============================================================
    public function create(Request $request)
    {
        $user = Auth::user();

        // Obtener activos disponibles para la transferencia
        $queryActivos = ActivoFijo::with(['categoria', 'ambiente.carrera', 'custodio'])
            ->paraUsuario($user)
            ->where('estado_registro', '!=', 'BAJA')
            ->where('estado_registro', '!=', 'EN_TRANSFERENCIA');

        if ($request->filled('q')) {
            $bus = $request->q;
            $queryActivos->where(function ($s) use ($bus) {
                $s->where('codigo_activo', 'like', "%{$bus}%")
                  ->orWhere('nombre', 'like', "%{$bus}%");
            });
        }

        $activosDisponibles = $queryActivos->orderBy('nombre')->get();

        // Obtener ambientes de destino disponibles
        $ambientesDisponibles = Ambiente::with('carrera')->orderBy('nombre')->get();

        // Obtener usuarios activos (nuevos custodios)
        $usuarios = User::where('estado', 'ACTIVO')
            ->with(['items' => fn($q) => $q->whereNull('item_usuario.fecha_fin')])
            ->orderBy('nombre_completo')
            ->get();

        return view('actas.transferencia.create', compact('activosDisponibles', 'ambientesDisponibles', 'usuarios'));
    }
// ============================================================
    // STORE — Registrar la transferencia
    // ============================================================
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->puedeSolicitarBajaOTransferencia()) {
            abort(403, 'No tiene permisos para solicitar transferencias.');
        }

        $data = $request->validate([
            'id_activo'           => 'required|exists:activos_fijos,id_activo',
            'id_ambiente_destino' => 'required|exists:ambientes,id_ambiente',
            'id_custodio'         => 'required|exists:usuarios,id_usuario',
            'observaciones'       => 'nullable|string|max:500',
            'fecha_transferencia' => 'required|date',
        ], [
            'id_activo.required'           => 'Debe seleccionar un activo.',
            'id_ambiente_destino.required' => 'Debe seleccionar el ambiente destino.',
            'id_custodio.required'         => 'Debe seleccionar el nuevo custodio.',
            'fecha_transferencia.required' => 'La fecha de transferencia es obligatoria.',
        ]);

        $activo = ActivoFijo::with(['categoria.ambiente'])->findOrFail($data['id_activo']);

        $permitidos = ActivoFijo::select('id_activo')->paraUsuario($user)->pluck('id_activo');
        if (! $permitidos->contains($activo->id_activo)) {
            abort(403, 'El activo no pertenece a su ámbito.');
        }

        if ($activo->estado_registro === 'BAJA') {
            return back()->withInput()->with('error', 'No se puede transferir un activo dado de baja.');
        }

        if ($activo->estado_registro === 'EN_TRANSFERENCIA') {
            return back()->withInput()->with('error', 'Este activo ya tiene una transferencia pendiente de aprobación.');
        }

        if ((int) $data['id_custodio'] === (int) $activo->id_custodio && (int) $data['id_ambiente_destino'] === (int) $activo->id_ambiente) {
            return back()->withInput()->with('error', 'El activo ya pertenece a ese custodio y se encuentra en el mismo ambiente.');
        }

        $numero = 'TRANS-' . now()->format('Y') . '-' . str_pad(
            (string) ((ActaTransferencia::max('id_acta_transferencia') ?? 0) + 1),
            4, '0', STR_PAD_LEFT
        );

        $esAdmin = $user->esAdmin();

        $acta = DB::transaction(function () use ($data, $activo, $user, $numero, $esAdmin) {
            $acta = ActaTransferencia::create([
                'numero_acta'          => $numero,
                'id_activo'            => $activo->id_activo,
                'id_origen_ambiente'   => $activo->id_ambiente,
                'id_destino_ambiente'  => $data['id_ambiente_destino'],
                'id_custodio_anterior' => $activo->id_custodio ?? $user->id_usuario,
                'id_custodio_nuevo'    => $data['id_custodio'],
                'fecha_transferencia'  => $data['fecha_transferencia'],
                'observaciones'        => $data['observaciones'] ?? null,
                'estado'               => $esAdmin ? ActaTransferencia::ESTADO_APROBADA : ActaTransferencia::ESTADO_PENDIENTE,
                'id_aprobador'         => $esAdmin ? $user->id_usuario : null,
                'fecha_aprobacion'     => $esAdmin ? now() : null,
                'estado_anterior'      => $activo->estado_registro,
            ]);

            if ($esAdmin) {
                // ⭐ Localizar una categoría existente en el ambiente destino que coincida en nombre,
                // o tomar la primera categoría válida del nuevo ambiente de destino.
                $categoriaDestino = \App\Models\Categoria::where('id_ambiente', $data['id_ambiente_destino'])
                    ->where('nombre', $activo->categoria->nombre ?? '')
                    ->first()
                    ?? \App\Models\Categoria::where('id_ambiente', $data['id_ambiente_destino'])->first();

                // Si el ambiente destino no tiene ninguna categoría creada todavía, creamos una por defecto
                if (! $categoriaDestino) {
                    $categoriaDestino = \App\Models\Categoria::create([
                        'id_ambiente' => $data['id_ambiente_destino'],
                        'codigo'      => 'CAT-' . rand(100, 999),
                        'nombre'      => 'General - ' . ($activo->categoria->nombre ?? 'Activos'),
                    ]);
                }

                // ⭐ Traslado completo e inmediato de todos los datos y su nueva ubicación/carrera
                $activo->update([
                    'id_ambiente'     => $data['id_ambiente_destino'],
                    'id_categoria'    => $categoriaDestino->id_categoria,
                    'id_custodio'     => $data['id_custodio'],
                    'estado_registro' => 'ASIGNADO',
                ]);
            } else {
                $activo->update([
                    'estado_registro' => 'EN_TRANSFERENCIA',
                ]);
            }

            return $acta;
        });

        $mensaje = $esAdmin
            ? "Transferencia {$numero} registrada y aplicada de inmediato al nuevo ambiente."
            : "Transferencia {$numero} solicitada. Queda pendiente de aprobación de la administración.";

        return redirect()
            ->route('actas.transferencia.show', $acta)
            ->with('status', $mensaje);
    }
    // ============================================================
    // APROBAR — Solo administración. Aquí sí se mueve el bien.
    // ============================================================
    public function aprobar(Request $request, ActaTransferencia $acta)
    {
        $this->autorizarAprobacion($acta);

        $data = $request->validate([
            'comentario' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($acta, $data, $request) {
            $acta->update([
                'estado'              => ActaTransferencia::ESTADO_APROBADA,
                'id_aprobador'        => $request->user()->id_usuario,
                'fecha_aprobacion'    => now(),
                'comentario_aprobacion' => $data['comentario'] ?? null,
            ]);

            $acta->activo?->update([
                'id_ambiente'     => $acta->id_destino_ambiente,
                'id_custodio'     => $acta->id_custodio_nuevo,
                'estado_registro' => 'ASIGNADO',
            ]);
        });

        return back()->with('status', "Transferencia {$acta->numero_acta} aprobada. El bien ya figura en el ambiente destino.");
    }

    // ============================================================
    // RECHAZAR — Deja el bien como estaba.
    // ============================================================
    public function rechazar(Request $request, ActaTransferencia $acta)
    {
        $this->autorizarAprobacion($acta);

        $data = $request->validate([
            'comentario' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($acta, $data, $request) {
            $acta->update([
                'estado'              => ActaTransferencia::ESTADO_RECHAZADA,
                'id_aprobador'        => $request->user()->id_usuario,
                'fecha_aprobacion'    => now(),
                'comentario_aprobacion' => $data['comentario'] ?? 'Rechazado por la administración.',
            ]);

            $acta->activo?->update([
                'estado_registro' => $acta->estado_anterior ?: 'ASIGNADO',
            ]);
        });

        return back()->with('status', "Transferencia {$acta->numero_acta} rechazada. El bien sigue en su ambiente de origen.");
    }

    protected function autorizarAprobacion(ActaTransferencia $acta): void
    {
        abort_unless(Auth::user()?->puedeAprobar(), 403, 'Solo la administración aprueba transferencias.');
        abort_if(! $acta->esPendiente(), 422, 'Esta transferencia ya fue procesada.');
    }

    // ============================================================
    // SHOW — Ver detalle de una transferencia
    // ============================================================
    public function show(ActaTransferencia $acta)
    {
        $this->authorizeActa($acta);

        $acta->load([
            'activo.categoria',
            'activo.ambiente.carrera',
            'activo.fuente',
            'origenAmbiente.carrera',
            'destinoAmbiente.carrera',
            'custodioAnterior.carrera',
            'custodioNuevo.carrera',
        ]);

        return view('actas.transferencia.show', compact('acta'));
    }

    // ============================================================
    // PDF — Acta de Transferencia en tamaño OFICIO HORIZONTAL
    // ============================================================
    public function pdf(ActaTransferencia $acta)
    {
        $this->authorizeActa($acta);

        $acta->load([
            'activo.categoria',
            'activo.ambiente.carrera',
            'activo.fuente',
            'origenAmbiente.carrera',
            'destinoAmbiente.carrera',
            'custodioAnterior.carrera',
            'custodioNuevo.carrera',
        ]);

        $pdf = PdfHelper::oficio('actas.transferencia.pdf', compact('acta'), 'landscape');
        return $pdf->stream('Acta_Transferencia_' . $acta->numero_acta . '.pdf');
    }

    // ============================================================
    // EXCEL — Acta de Transferencia en formato tabular
    // ============================================================
    public function excel(ActaTransferencia $acta)
    {
        $this->authorizeActa($acta);

        $acta->load([
            'activo.categoria',
            'activo.ambiente.carrera',
            'activo.fuente',
            'origenAmbiente.carrera',
            'destinoAmbiente.carrera',
            'custodioAnterior.carrera',
            'custodioNuevo.carrera',
        ]);

        return response()->view('actas.transferencia.excel', compact('acta'));
    }
    protected function authorizeActa(ActaTransferencia $acta): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'No autenticado.');
        }

        if ($user->esAdmin()) {
            return;
        }

        if ((int) $acta->id_custodio_anterior === (int) $user->id_usuario) {
            return;
        }
        if ((int) $acta->id_custodio_nuevo === (int) $user->id_usuario) {
            return;
        }

        if ($user->esOperativo()) {
            return;
        }

        $ambientesVisibles = $user->idsAmbientesVisibles();

        if (! empty($ambientesVisibles)) {
            $visibles = $ambientesVisibles
                ->map(fn ($id) => (int) $id)
                ->all();

            if (in_array((int) $acta->id_origen_ambiente, $visibles, true)
                || in_array((int) $acta->id_destino_ambiente, $visibles, true)) {
                return;
            }
        }

        abort(403, 'No autorizado para ver esta acta.');
    }
}
