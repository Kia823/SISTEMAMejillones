<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleActaActivo extends Model
{
    use HasFactory;

    protected $table = 'detalle_acta_activos';

    protected $fillable = ['acta_id', 'activo_id', 'observaciones'];

    public function acta()
    {
        return $this->belongsTo(ActaRecepcionAsignacion::class, 'acta_id');
    }

    public function activo()
    {
        return $this->belongsTo(ActivoFijo::class, 'activo_id');
    }
}
