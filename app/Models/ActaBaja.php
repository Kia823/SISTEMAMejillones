<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActaBaja extends Model
{
    use HasFactory;

    protected $table = 'actas_baja';
    protected $primaryKey = 'id_acta_baja';

    protected $fillable = [
        'numero_acta',
        'id_activo',
        'id_usuario',
        'motivo',
        'fecha_baja',
        'archivo_resolucion',
        'estado',
        'id_aprobador',
        'fecha_aprobacion',
        'comentario_aprobacion',
        'es_sustitucion',
        'datos_sustituto',
        'id_activo_sustituto',
    ];

    protected $casts = [
        'fecha_baja'          => 'date',
        'fecha_aprobacion'    => 'datetime',
        'datos_sustituto'     => 'array',
        'es_sustitucion'      => 'boolean',
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

    /**
     * Usuario que registró la solicitud (inventariador o ayudante).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Administradora que aprobó o rechazó el acta.
     */
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'id_aprobador', 'id_usuario');
    }

    /**
     * Equipo nuevo que entra a sustituir al dado de baja.
     */
    public function activoSustituto()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_activo_sustituto', 'id_activo');
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

    /**
     * Badge con el color según el estado, para las vistas.
     */
    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_APROBADA  => 'green',
            self::ESTADO_RECHAZADA => 'red',
            default                => 'amber',
        };
    }
    public function actaBaja()
    {
        return $this->hasOne(ActaBaja::class, 'id_activo', 'id_activo');
    }
}
