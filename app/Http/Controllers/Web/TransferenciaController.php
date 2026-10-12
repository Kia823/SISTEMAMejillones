<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivoFijo;
use App\Models\ActaTransferenciaInterna;
use App\Models\User;
use App\Models\Ambiente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferenciaController extends Controller
{
    public function index(Request $request)
    {
        $me = Auth::id();
        $actas = ActaTransferenciaInterna::with(['activo', 'responsableSaliente', 'responsableEntrante'])
            ->where('responsable_saliente_id', $me)
            ->orWhere('responsable_entrante_id', $me)
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return view('actas.transferencia.index', compact('actas'));
    }

    public function create(Request $request)
    {
        $activos = ActivoFijo::with(['categoria', 'ambiente.carrera', 'custodioActual'])
            ->paraUsuario(Auth::user())
            ->where('estado_registro', '!=', 'BAJA')
            ->when($request->filled('q'), function ($q) use ($request) {
                $bus = $request->q;
                $q->where(function ($s) use ($bus) {
                    $s->where('codigo_activo', 'like', "%{$bus}%")
                      ->orWhere('nombre', 'like', "%{$bus}%");
                });
            })
            ->get();

        $destinos = User::where('estado', 'ACTIVO')->orderBy('nombre_completo')->get();

        return view('actas.transferencia.create', compact('activos', 'destinos'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $esInventariador = method_exists($user, 'esInventariador') && $user->esInventariador();
        $esJefeCarrera = method_exists($user, 'esJefeCarrera') && $user->esJefeCarrera();
        $esCustodio = method_exists($user, 'esCustodio') && $user->esCustodio();
        if (! $esInventariador && ! $esJefeCarrera && ! $esCustodio) {
            abort(403, 'No tiene permisos para transferir.');
        }

        $data = $request->validate([
            'activo_id' => 'required|exists:activos_fijos,id',
            'responsable_entrante_id' => 'required|exists:usuarios,id',
            'motivo_transferencia' => 'required|string|max:500',
        ], [
            'activo_id.required' => 'Debe seleccionar un activo.',
            'responsable_entrante_id.required' => 'Debe elegir el destinatario.',
            'motivo_transferencia.required' => 'El motivo es obligatorio.',
        ]);

        // el activo debe estar dentro del scoping del usuario y activo
        $activo = ActivoFijo::findOrFail($data['activo_id']);
        $permitidos = ActivoFijo::select('id')->paraUsuario($user)->pluck('id');
        if (! $permitidos->contains($activo->id)) {
            abort(403, 'El activo no pertenece a su ámbito.');
        }
        if ($activo->estado_registro === 'BAJA') {
            return back()->withInput()->with('error', 'No se puede transferir un activo dado de baja.');
        }
        if ((int) $data['responsable_entrante_id'] === (int) $activo->custodio_actual_id) {
            return back()->withInput()->with('error', 'El activo ya pertenece a ese custodio.');
        }

        $numero = 'TRANS-' . now()->format('Y') . '-' . str_pad((string) ((ActaTransferenciaInterna::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        $acta = DB::transaction(function () use ($user, $activo, $data, $numero, $request) {
            $acta = ActaTransferenciaInterna::create([
                'numero_acta' => $numero,
                'fecha_acta' => now()->toDateString(),
                'responsable_saliente_id' => $activo->custodio_actual_id ?? $user->id,
                'responsable_entrante_id' => $data['responsable_entrante_id'],
                'activo_id' => $activo->id,
                'estado' => 'COMPLETADA',
                'motivo_transferencia' => $data['motivo_transferencia'],
                'observaciones' => $request->input('observaciones'),
            ]);

            $activo->update([
                'custodio_actual_id' => $data['responsable_entrante_id'],
                'estado_registro' => 'ASIGNADO',
            ]);

            return $acta;
        });

        session()->flash('status', 'Transferencia registrada. El activo ahora pertenece al nuevo custodio.');

        return $this->show($acta);
    }

    public function show(ActaTransferenciaInterna $acta)
    {
        $me = Auth::id();
        if (! $acta->responsable_saliente_id && ! $acta->responsable_entrante_id) {
            abort(404);
        }
        $esAdmin = is_callable([Auth::user(), 'esAdmin']) && call_user_func([Auth::user(), 'esAdmin']);
        if ($acta->responsable_saliente_id !== $me && $acta->responsable_entrante_id !== $me && ! $esAdmin) {
            abort(403, 'No autorizado.');
        }

        $acta->load(['activo.categoria', 'activo.ambiente.carrera', 'responsableSaliente', 'responsableEntrante']);

        return view('actas.transferencia.show', compact('acta'));
    }

    public function pdf(ActaTransferenciaInterna $acta)
    {
        $me = Auth::id();
        $user = Auth::user();
        $esAdmin = is_callable([$user, 'esAdmin']) && call_user_func([$user, 'esAdmin']);
        if ($acta->responsable_saliente_id !== $me && $acta->responsable_entrante_id !== $me && ! $esAdmin) {
            abort(403, 'No autorizado.');
        }
        $acta->load(['activo.categoria', 'activo.ambiente.carrera', 'responsableSaliente', 'responsableEntrante']);

        $pdf = Pdf::loadView('actas.transferencia.pdf', compact('acta'))
            ->setPaper('letter', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream('Acta_Transferencia_' . $acta->numero_acta . '.pdf');
    }
}
