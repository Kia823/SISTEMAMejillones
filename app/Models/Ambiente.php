<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    use HasFactory;

    protected $table      = 'ambientes';
    protected $primaryKey = 'id_ambiente';

    protected $fillable = [
        'codigo', 'nombre', 'id_carrera', 'bloque', 'piso',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera', 'id_carrera');
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'id_ambiente', 'id_ambiente');
    }

    public function activos()
    {
        return $this->hasMany(ActivoFijo::class, 'id_ambiente', 'id_ambiente');
    }
    public function custodios()
    {
        return $this->belongsToMany(User::class, 'ambiente_user', 'id_ambiente', 'id_usuario');
    }
}
