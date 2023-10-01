<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiquidacionLavadorPago extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'liquidacion_lavador_pagos';
    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',

        'lavador_id_user',
        'lavador_id_cliente',
        'fecha_pagado',
        'total_servicios',
        'cuenta_por_pagar',
        'liquido_pagable',
        'detalles_id',

        'estado',
        'deleted_at',
    ];

    public function lavador(){
        return $this->belongsTo('App\Models\User', 'lavador_id_user');
    }
}
