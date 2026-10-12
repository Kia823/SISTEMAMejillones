<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificacionFisica extends Model
{
    use HasFactory;

    protected $table      = 'verificaciones_fisicas';
    protected $primaryKey = 'id_verificacion';

    // ⭐ Nombres idénticos a los campos definidos en la migración de tu base de datos
    protected $fillable = [
        'id_activo',
        'id_ambiente_escaneo',
        'id_verificador',
        'fecha_verificacion',
        'es_correspondencia_correcta',
        'sincronizado_offline',
        'dispositivo_uuid',
        'datos_extra',
        'estado_fisico_reportado',
        'observaciones',
        'estado_aprobacion',
        'id_aprobador',
        'fecha_aprobacion',
        'comentario_aprobacion',
        'cambio_estado_fisico_solicitado',
        'estado_fisico_solicitado',
        'solicitar_baja',
        'verificacion_finalizada',
    ];

    protected $casts = [
        'fecha_verificacion'              => 'datetime',
        'es_correspondencia_correcta'     => 'boolean',
        'sincronizado_offline'            => 'boolean',
        'datos_extra'                     => 'array',
        'fecha_aprobacion'                => 'datetime',
        'cambio_estado_fisico_solicitado' => 'boolean',
        'solicitar_baja'                  => 'boolean',
        'verificacion_finalizada'         => 'boolean',
    ];

    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_APROBADA  = 'APROBADA';
    const ESTADO_RECHAZADA = 'RECHAZADA';
    const ESTADO_CORREGIDA = 'CORREGIDA';

    // ==========================================
    // RELACIONES (Alineadas con tu esquema)
    // ==========================================
    public function activo()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_activo', 'id_activo');
    }

    public function ambienteEscaneo()
    {
        return $this->belongsTo(Ambiente::class, 'id_ambiente_escaneo', 'id_ambiente');
    }

    public function verificador()
    {
        return $this->belongsTo(User::class, 'id_verificador', 'id_usuario');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'id_aprobador', 'id_usuario');
    }

    // ==========================================
    // SCOPES Y MÉTODOS
    // ==========================================
    public function scopePendientesAprobacion($query)
    {
        return $query->where('estado_aprobacion', self::ESTADO_PENDIENTE);
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado_aprobacion', self::ESTADO_APROBADA);
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado_aprobacion', self::ESTADO_RECHAZADA);
    }

    public function esPendiente(): bool
    {
        return $this->estado_aprobacion === self::ESTADO_PENDIENTE;
    }

    public function esAprobada(): bool
    {
        return $this->estado_aprobacion === self::ESTADO_APROBADA;
    }
}
