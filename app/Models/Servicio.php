<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory ,SoftDeletes;
    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',

        'descripcion',
        'categoria_id',
        'unidad_venta',
        'precio',

        'estado',
        'deleted_at',
    ];

    public function categoria(){
        return $this->belongsTo('App\Models\Categoria', 'categoria_id');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function movimientosFinalizados()
    {
        return $this->hasMany(Movimiento::class)
            ->whereHas('detalle', function ($q) {
                $q->where('estado', 'Finalizado');
            });
    }
}
