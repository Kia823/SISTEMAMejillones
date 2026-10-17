<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ambiente;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    // ============================================================
    // INDEX — Público (autenticado)
    // ============================================================
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Categoria::with(['ambiente.carrera']);

        // Filtrar por alcance del usuario
        if ($user->esAdmin()) {
            // administradora ve todo
        } elseif ($user->esJefeCarrera()) {
            $query->whereHas('ambiente', fn ($q) => $q->where('id_carrera', $user->id_carrera));
        } else {
            $idsAmbientes = $user->idsAmbientesVisibles();
            if ($idsAmbientes !== null && count($idsAmbientes) > 0) {
                $query->whereIn('id_ambiente', $idsAmbientes);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $ambiente = null;
        if ($request->filled('id_ambiente')) {
            $ambiente = Ambiente::find($request->id_ambiente);
            $query->where('id_ambiente', $request->id_ambiente);
        }

        $categorias = $query->withCount('activos')
            ->orderBy('nombre')
            ->paginate(15);

        // Ambientes para el filtro (según alcance del usuario)
        if ($user->esAdmin()) {
            $ambientesFiltro = Ambiente::with('carrera')->orderBy('nombre')->get();
        } elseif ($user->esJefeCarrera()) {
            $ambientesFiltro = Ambiente::with('carrera')->where('id_carrera', $user->id_carrera)->orderBy('nombre')->get();
        } else {
            $idsAmbientes = $user->idsAmbientesVisibles();
            $ambientesFiltro = $idsAmbientes && count($idsAmbientes) > 0
                ? Ambiente::with('carrera')->whereIn('id_ambiente', $idsAmbientes)->orderBy('nombre')->get()
                : collect();
        }

        return view('categorias.index', compact('categorias', 'ambiente', 'ambientesFiltro'));
    }

    // ============================================================
    // SHOW — Delega a ActivoController
    // ============================================================
    public function show($id)
    {
        return app(ActivoController::class)->show($id);
    }

    // ============================================================
    // STORE — Solo admin
    // ============================================================
    public function store(Request $request)
    {
        // ✅ CORREGIDO: usa esAdmin() del modelo User
        $user = Auth::user();
        if (! $user || ! $user->esAdmin()) {
            abort(403, 'No autorizado.');
        }

        $request->validate([
            'codigo'           => 'required|string|max:20|unique:categorias,codigo',
            'nombre' => 'required|string|max:100',
            'descripcion'      => 'nullable|string|max:255',
            'id_ambiente'      => 'required|exists:ambientes,id_ambiente',
        ], [
            'codigo.required'           => 'El código de la categoría es obligatorio.',
            'codigo.unique'             => 'Este código ya existe.',
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'id_ambiente.required'      => 'El ambiente es obligatorio.',
        ]);

        Categoria::create([
            'codigo'      => strtoupper($request->codigo),
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'id_ambiente' => $request->id_ambiente,
        ]);

        return back()->with('success', 'Categoría creada exitosamente.');
    }

    // ============================================================
    // UPDATE — Solo admin
    // ============================================================
    public function update(Request $request, $id)
    {
        // ✅ CORREGIDO
        $user = Auth::user();
        if (! $user || ! $user->esAdmin()) {
            abort(403, 'No autorizado.');
        }

        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'codigo' => [
                'required', 'string', 'max:20',
                Rule::unique('categorias', 'codigo')
                    ->ignore($categoria->id_categoria, 'id_categoria'),
            ],
            'nombre' => 'required|string|max:100',
            'descripcion'      => 'nullable|string|max:255',
        ]);

        $categoria->update([
            'codigo'      => strtoupper($request->codigo),
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Categoría actualizada correctamente.');
    }

    // ============================================================
    // DESTROY — Solo admin
    // ============================================================
    public function destroy($id)
    {
        // ✅ CORREGIDO
        $user = Auth::user();
        if (! $user || ! $user->esAdmin()) {
            abort(403, 'No autorizado.');
        }

        $categoria = Categoria::findOrFail($id);

        if ($categoria->activos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene activos asociados.');
        }

        $categoria->delete();

        return back()->with('success', 'Categoría eliminada correctamente.');
    }
}
