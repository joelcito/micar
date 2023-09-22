<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiquidacionLavador extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'liquidacion_lavadores';
    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',

        'lavador_id',
        'servicio_id',
        'porcentaje',

        'estado',
        'deleted_at',
    ];

    public function servicio(){
        return $this->belongsTo('App\Models\Servicio', 'servicio_id');
    }
}
