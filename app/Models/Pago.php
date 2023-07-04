<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',

        'factura_id',
        'vehiculo_id',
        'importe',
        'total',
        'fecha',

        'estado',
        'deleted_at',
    ];

    public function vehiculo(){
        return $this->belongsTo('App\Models\Vehiculo', 'vehiculo_id');
    }

    public function lavador(){
        return $this->belongsTo('App\Models\User', 'lavador_id');
    }

    public function servicio(){
        return $this->belongsTo('App\Models\Servicio', 'servicio_id');
    }

}
