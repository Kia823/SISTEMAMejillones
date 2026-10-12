<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialTitular extends Model
{
    protected $table = 'historial_titulares';
    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'id_item', 'id_usuario_anterior', 'id_usuario_nuevo',
        'nombre_anterior', 'nombre_nuevo', 'tipo',
        'fecha_evento', 'motivo', 'id_usuario_registro',
    ];

    protected $casts = [
        'fecha_evento' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item', 'id_item');
    }

    public function usuarioAnterior()
    {
        return $this->belongsTo(User::class, 'id_usuario_anterior', 'id_usuario');
    }

    public function usuarioNuevo()
    {
        return $this->belongsTo(User::class, 'id_usuario_nuevo', 'id_usuario');
    }
}
