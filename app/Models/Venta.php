<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',

        'lavador_id',
        'vehiculo_id',
        'servicio_id',
        'precio',
        'fecha',

        'estado',
        'deleted_at',
    ];

    public function lavador(){
        return $this->belongsTo('App\Models\User', 'lavador_id');
    }

    public function vehiculo(){
        return $this->belongsTo('App\Models\Vehiculo', 'vehiculo_id');
    }

    public function servicio(){
        return $this->belongsTo('App\Models\Servicio', 'servicio_id');
    }
}
