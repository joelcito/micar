<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model
{
    use HasFactory;
	use SoftDeletes;

    public function vehiculo(){
        return $this->belongsTo('App\Models\Vehiculo', 'vehiculo_id');
    }

    public function cliente(){
        return $this->belongsTo('App\Models\Cliente', 'cliente_id');
    }

    public static function facturasDeudoras($vehiculo, $cliente){
        $facturas = Factura::where('estado_pago', 'Deuda')
                            ->where('vehiculo_id',$vehiculo)
                            ->where('cliente_id',$cliente)
                            ->orderBy('id', 'asc')
                            ->get();

        return $facturas;
    }

    public function detalles(){
        return $this->hasMany('App\Models\Detalle', 'factura_id');
    }

    public function pagos(){
        return $this->hasMany(Pago::class);
    }

    public function creador(){
        return $this->belongsTo('App\Models\User', 'creador_id');
    }

}
