<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuenteFinanciamiento extends Model
{
    use HasFactory;

    protected $table      = 'fuentes_financiamiento';
    protected $primaryKey = 'id_fuente';

    protected $fillable = ['codigo', 'nombre'];

    public function activos()
    {
        return $this->hasMany(ActivoFijo::class, 'id_fuente', 'id_fuente');
    }
}
