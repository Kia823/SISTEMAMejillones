<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $table      = 'carreras';
    protected $primaryKey = 'id_carrera';

    protected $fillable = ['codigo', 'nombre'];

    public function ambientes()
    {
        return $this->hasMany(Ambiente::class, 'id_carrera', 'id_carrera');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_carrera', 'id_carrera');
    }

    /**
     * El jefe de carrera es quiencustodia los bienes de toda la carrera.
     * A fin de año recibe la liberación de cada ambiente.
     */
    public function jefeCarrera()
    {
        return $this->hasOne(User::class, 'id_carrera', 'id_carrera')
            ->where('rol', 'JEFE_CARRERA')
            ->where('estado', 'ACTIVO');
    }

}
