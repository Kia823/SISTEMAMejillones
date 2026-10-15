<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivoFijo;
use App\Models\Ambiente;
use App\Models\Carrera;
use App\Models\HistorialTitular;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    // ============================================================
    // INDEX: listado de ítems con sus custodios
    // ============================================================
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Item::with(['carrera', 'ambiente', 'custodios'])
            ->withCount(['activos', 'custodiosVigentes']);

        // El ítem es la unidad de custodia: cada quien ve los suyos y los de
        // sus ambientes.
        if (! $user->puedeVerTodo()) {
            $idsAmbientes = $user->idsAmbientesVisibles();

            $query->where(function ($q) use ($user, $idsAmbientes) {
                $q->whereHas('custodiosVigentes', fn ($sub) => $sub->where('usuarios.id_usuario', $user->id_usuario))
                    ->orWhere(function ($sub) use ($idsAmbientes) {
                        if (empty($idsAmbientes)) {
                            $sub->whereRaw('1 = 0');

                            return;
                        }

                        $sub->whereIn('items.id_ambiente', $idsAmbientes);
                    });
            });
        }

        if ($request->filled('estado') && $request->estado !== 'Todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        $items = $query->orderBy('numero_item')->get();

        return view('items.index', compact('items'));
    }

    // ============================================================
    // SHOW: detalle del ítem, sus custodios, sus activos y su historial
    // ============================================================
    public function show($numeroItem)
    {
        $item = Item::where('numero_item', $numeroItem)
            ->orWhere('id_item', is_numeric($numeroItem) ? $numeroItem : 0)
            ->firstOrFail();

        $this->autorizarItem($item);

        $item->load(['carrera', 'ambiente', 'historial.usuarioAnterior', 'historial.usuarioNuevo']);

        $custodios = $item->custodios()
            ->orderBy('usuarios.estado')
            ->orderBy('usuarios.nombre_completo')
            ->get();

        $activos = ActivoFijo::with(['categoria', 'ambiente', 'fuente', 'custodio'])
            ->paraUsuario(Auth::user())
            ->where('id_item', $item->id_item)
            ->orderBy('codigo_activo')
            ->get();

        $activosPorCustodio = $activos->whereNotNull('id_custodio')->groupBy('id_custodio');

        return view('items.show', compact('item', 'custodios', 'activos', 'activosPorCustodio'));
    }

    /**
     * Un ítem que no está dentro del alcance del usuario no se abre, ni
     * aunque se conozca su número.
     */
    protected function autorizarItem(Item $item): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->puedeVerTodo()) {
            return;
        }

        if ($item->custodiosVigentes()->where('usuarios.id_usuario', $user->id_usuario)->exists()) {
            return;
        }

        $idsAmbientes = $user->idsAmbientesVisibles();

        abort_if(
            $item->id_ambiente && ! empty($idsAmbientes)
                && ! in_array((int) $item->id_ambiente, array_map('intval', $idsAmbientes), true),
            403,
            'Ese ítem no le corresponde.'
        );

        abort_if(
            ! $item->id_ambiente && empty($idsAmbientes),
            403,
            'Ese ítem no le corresponde.'
        );
    }

    // ============================================================
    // STORE
    // ============================================================
    public function create()
    {
        return view('items.create', $this->datosFormulario());
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);

        return view('items.edit', array_merge($this->datosFormulario(), compact('item')));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $item = Item::create([
            'numero_item' => $data['numero_item'],
            'descripcion' => $data['descripcion'] ?? null,
            'id_carrera' => $data['id_carrera'] ?? null,
            'id_ambiente' => $data['id_ambiente'] ?? null,
            'unidad' => $data['unidad'] ?? null,
            'estado' => 'VACANTE',
        ]);

        HistorialTitular::create([
            'id_item' => $item->id_item,
            'tipo' => 'CREACION',
            'fecha_evento' => now()->toDateString(),
            'motivo' => 'Alta del ítem '.$item->numero_item,
            'id_usuario_registro' => Auth::id(),
        ]);

        return redirect()
            ->route('items.show', $item->numero_item)
            ->with('success', 'Ítem '.$item->numero_item.' registrado correctamente.');
    }

    // ============================================================
    // UPDATE
    // ============================================================
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $data = $this->validar($request, $item);

        $item->update([
            'numero_item' => $data['numero_item'],
            'descripcion' => $data['descripcion'] ?? null,
            'id_carrera' => $data['id_carrera'] ?? null,
            'id_ambiente' => $data['id_ambiente'] ?? null,
            'unidad' => $data['unidad'] ?? null,
        ]);

        return back()->with('success', 'Ítem '.$item->numero_item.' actualizado.');
    }

    // ============================================================
    // ASIGNAR / DESVINCULAR CUSTODIO
    // Un ítem admite 2, 3 o más custodios. Ninguna de estas acciones
    // toca los activos: el activo pertenece al ítem, no a la persona.
    // ============================================================
    public function asignarCustodio(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $data = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'tipo' => 'nullable|in:TITULAR,SECUNDARIO,RESPONSABLE',
            'fecha_inicio' => 'nullable|date',
        ]);

        $usuario = User::findOrFail($data['id_usuario']);

        if ($item->custodios()->where('usuarios.id_usuario', $usuario->id_usuario)->whereNull('item_usuario.fecha_fin')->exists()) {
            return back()->with('error', $usuario->nombre_completo.' ya es custodio vigente de este ítem.');
        }

        DB::transaction(function () use ($item, $usuario, $data) {
            $item->custodios()->syncWithoutDetaching([
                $usuario->id_usuario => [
                    'tipo' => $data['tipo'] ?? 'TITULAR',
                    'fecha_inicio' => $data['fecha_inicio'] ?? now()->toDateString(),
                    'fecha_fin' => null,
                ],
            ]);

            $item->recalcularEstado();

            HistorialTitular::create([
                'id_item' => $item->id_item,
                'id_usuario_nuevo' => $usuario->id_usuario,
                'nombre_nuevo' => $usuario->nombre_completo,
                'tipo' => 'ASIGNACION',
                'fecha_evento' => now()->toDateString(),
                'motivo' => 'Asignación de custodio',
                'id_usuario_registro' => Auth::id(),
            ]);
        });

        return back()->with('success', $usuario->nombre_completo.' quedó como custodio del ítem '.$item->numero_item.'.');
    }

    public function desvincularCustodio(Request $request, $id, $idUsuario)
    {
        $item = Item::findOrFail($id);
        $usuario = User::findOrFail($idUsuario);

        $pivot = $item->custodios()->where('usuarios.id_usuario', $usuario->id_usuario)->first();

        if (! $pivot) {
            return back()->with('error', 'Esa persona no es custodio de este ítem.');
        }

        $request->validate([
            'motivo' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($item, $usuario, $request, $pivot) {
            $pivot->pivot->update([
                'fecha_fin' => now()->toDateString(),
            ]);

            $item->recalcularEstado();

            HistorialTitular::create([
                'id_item' => $item->id_item,
                'id_usuario_anterior' => $usuario->id_usuario,
                'nombre_anterior' => $usuario->nombre_completo,
                'tipo' => 'DETACH',
                'fecha_evento' => now()->toDateString(),
                'motivo' => $request->input('motivo') ?: 'Custodio desvinculado. Los activos permanecen en el ítem.',
                'id_usuario_registro' => Auth::id(),
            ]);
        });

        return back()->with('success', $usuario->nombre_completo.' dejó de ser custodio. Los activos siguen asignados al ítem '.$item->numero_item.'.');
    }

    // ============================================================
    // DESTROY
    // ============================================================
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        $activos = $item->activos()->count();

        if ($activos > 0) {
            return back()->with('error', "No se puede eliminar el ítem \"{$item->numero_item}\" porque tiene {$activos} activo(s) asignado(s).");
        }

        $numero = $item->numero_item;
        $item->delete();

        return redirect()->route('items.index')->with('success', "Ítem {$numero} eliminado.");
    }

    // ============================================================
    // BUSCAR (AJAX)
    // ============================================================
    public function buscar(Request $request)
    {
        $items = Item::with('custodios')
            ->buscar($request->input('q'))
            ->orderBy('numero_item')
            ->limit(20)
            ->get();

        return response()->json($items->map(fn ($item) => [
            'id_item' => $item->id_item,
            'numero_item' => $item->numero_item,
            'descripcion' => $item->descripcion,
            'custodios' => $item->custodios->pluck('nombre_completo'),
        ]));
    }

    // ============================================================
    // DATOS PARA LOS FORMULARIOS
    // ============================================================
    public function datosFormulario()
    {
        return [
            'carreras'  => Carrera::orderBy('nombre')->get(),
            'ambientes' => Ambiente::with('carrera')->orderBy('nombre')->get(),
        ];
    }

    private function validar(Request $request, ?Item $item = null): array
    {
        return $request->validate([
            'numero_item' => [
                'required', 'string', 'max:20',
                Rule::unique('items', 'numero_item')->ignore($item?->id_item, 'id_item'),
            ],
            'descripcion' => 'nullable|string|max:150',
            'id_carrera' => 'nullable|exists:carreras,id_carrera',
            'id_ambiente' => 'nullable|exists:ambientes,id_ambiente',
            'unidad' => 'nullable|string|max:100',
        ], [
            'numero_item.required' => 'El número de ítem es obligatorio.',
            'numero_item.unique' => 'Ya existe un ítem registrado con ese número.',
        ]);
    }
}
