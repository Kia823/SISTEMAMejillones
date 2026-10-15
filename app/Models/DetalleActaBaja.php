<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleActaBaja extends Model
{
    protected $table = 'detalle_acta_bajas';

    protected $fillable = ['acta_id', 'activo_id', 'observaciones'];

    public function acta()
    {
        return $this->belongsTo(ActaBaja::class, 'acta_id');
    }

    public function activo()
    {
        return $this->belongsTo(ActivoFijo::class, 'activo_id');
    }
}
