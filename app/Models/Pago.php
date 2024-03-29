<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'pagos';
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

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'creador_id');
    }

    // public function lavador(){
    //     return $this->belongsTo('App\Models\User', 'lavador_id');
    // }

    // public function servicio(){
    //     return $this->belongsTo('App\Models\Servicio', 'servicio_id');
    // }


}
