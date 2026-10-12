<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $table = 'items';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'numero_item', 'descripcion', 'id_carrera', 'id_ambiente', 'unidad', 'estado', 'fecha_vacancia',
    ];

    protected $casts = [
        'fecha_vacancia' => 'date',
        'estado' => 'string',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    /**
     * El ambiente donde se presta el servicio. Sus custodios son los que
     * liberan los bienes al jefe de carrera al cierre de la gestión.
     */
    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'id_ambiente', 'id_ambiente');
    }

    /**
     * Todos los custodios del ítem, incluidos los que ya renunciaron.
     */
    public function custodios()
    {
        return $this->belongsToMany(User::class, 'item_usuario', 'id_item', 'id_usuario')
            ->withPivot(['tipo', 'fecha_inicio', 'fecha_fin'])
            ->withTimestamps();
    }

    /**
     * Solo los custodios vigentes: estado ACTIVO y sin fecha_fin.
     */
    public function custodiosVigentes()
    {
        return $this->custodios()
            ->where('usuarios.estado', 'ACTIVO')
            ->whereNull('item_usuario.fecha_fin');
    }

    public function activos()
    {
        return $this->hasMany(ActivoFijo::class, 'id_item', 'id_item');
    }

    public function historial()
    {
        return $this->hasMany(HistorialTitular::class, 'id_item', 'id_item')
            ->orderByDesc('fecha_evento')
            ->orderByDesc('id_historial');
    }

    public function tieneCustodios(): bool
    {
        return $this->custodiosVigentes()->exists();
    }

    public function getEstadoTextoAttribute(): string
    {
        return $this->estado === 'OCUPADO' ? 'Con custodios' : 'Vacante';
    }

    public function getDescripcionCompletaAttribute(): string
    {
        return trim(($this->descripcion ?? '') . ' ' . ($this->unidad ?? ''));
    }

    /**
     * Recalcula estado del ítem según tenga o no custodios vigentes.
     */
    public function recalcularEstado(): void
    {
        $ocupado = $this->custodiosVigentes()->exists();

        $this->forceFill([
            'estado' => $ocupado ? 'OCUPADO' : 'VACANTE',
            'fecha_vacancia' => $ocupado ? null : ($this->fecha_vacancia ?? now()->toDateString()),
        ])->save();
    }

    public function scopeBuscar($query, $termino)
    {
        $termino = trim((string) $termino);

        if ($termino === '') {
            return $query;
        }

        return $query->where(function ($q) use ($termino) {
            $q->where('numero_item', 'like', "%{$termino}%")
                ->orWhere('descripcion', 'like', "%{$termino}%")
                ->orWhere('unidad', 'like', "%{$termino}%")
                ->orWhereHas('custodios', function ($cq) use ($termino) {
                    $cq->where('nombre_completo', 'like', "%{$termino}%")
                        ->orWhere('ci', 'like', "%{$termino}%");
                });
        });
    }
}
