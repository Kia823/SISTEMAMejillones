<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaTransferencia extends Model
{
    use HasFactory;

    protected $table      = 'actas_transferencia';
    protected $primaryKey = 'id_acta_transferencia';

    protected $fillable = [
        'numero_acta',
        'id_activo',
        'id_origen_ambiente',
        'id_destino_ambiente',
        'id_custodio_anterior',
        'id_custodio_nuevo',
        'fecha_transferencia',
        'observaciones',
        'estado',
        'id_aprobador',
        'fecha_aprobacion',
        'comentario_aprobacion',
        'estado_anterior',
    ];

    protected $casts = [
        'fecha_transferencia' => 'date',
        'fecha_aprobacion'    => 'datetime',
        'estado'              => 'string',
    ];

    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_APROBADA  = 'APROBADA';
    const ESTADO_RECHAZADA = 'RECHAZADA';

    // ==========================================
    // RELACIONES
    // ==========================================

    public function activo()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_activo', 'id_activo');
    }

    public function origenAmbiente()
    {
        return $this->belongsTo(Ambiente::class, 'id_origen_ambiente', 'id_ambiente');
    }

    public function destinoAmbiente()
    {
        return $this->belongsTo(Ambiente::class, 'id_destino_ambiente', 'id_ambiente');
    }

    public function custodioAnterior()
    {
        return $this->belongsTo(User::class, 'id_custodio_anterior', 'id_usuario');
    }

    public function custodioNuevo()
    {
        return $this->belongsTo(User::class, 'id_custodio_nuevo', 'id_usuario');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'id_aprobador', 'id_usuario');
    }

    // ==========================================
    // ESTADO
    // ==========================================
    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function esPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function esAprobada(): bool
    {
        return $this->estado === self::ESTADO_APROBADA;
    }

    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_APROBADA  => 'Aprobada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            default                => 'Pendiente de aprobación',
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_APROBADA  => 'green',
            self::ESTADO_RECHAZADA => 'red',
            default                => 'amber',
        };
    }
}