<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios';
    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'tabla_afectada',
        'registro_id',
        'usuario_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_origen',
        'user_agent',
        'fecha_cambio',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
        'fecha_cambio'     => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id_usuario');
    }
}
