<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActaRecepcionAsignacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actas_recepcion_asignacion';

    protected $fillable = [
        'numero_acta', 'fecha_acta', 'observaciones',
        'responsable_bien_id', 'custodio_temporal_id',
        'inventariador_id', 'estado',
    ];

    protected $casts = [
        'fecha_acta' => 'date',
    ];

    public function responsableBien()
    {
        return $this->belongsTo(User::class, 'responsable_bien_id');
    }

    public function custodioTemporal()
    {
        return $this->belongsTo(User::class, 'custodio_temporal_id');
    }

    public function inventariador()
    {
        return $this->belongsTo(User::class, 'inventariador_id');
    }

    public function activos()
    {
        return $this->belongsToMany(ActivoFijo::class, 'detalle_acta_activos', 'acta_id', 'activo_id')
            ->withPivot('observaciones')
            ->withTimestamps();
    }
}
