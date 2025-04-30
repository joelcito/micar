<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Detalle extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'detalles';
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

    public static function detallesLavadorFecha($lavador, $fecha_ini, $fecha_fin){

        // $detalles = Detalle::select(
        //             'detalles.servicio_id',
        //             DB::raw('SUM(detalles.cantidad) as cantidad'),
        //             'servicios.precio',
        //             'servicios.descripcion',
        //             'servicios.liquidacion as liquidacionServicio',
        //             'servicios.tipo_liquidacion as tipoLiquidacionServicio',
        //             'liquidacion_lavadores.tipo_liquidacion as tipoLiquidacionLl',
        //             'liquidacion_lavadores.liquidacion as liquidacionLl'
        //         )
        //         ->join('servicios', 'detalles.servicio_id', '=', 'servicios.id')
        //         ->leftJoin('liquidacion_lavadores', 'detalles.servicio_id', '=', 'liquidacion_lavadores.servicio_id')
        //         ->where('detalles.lavador_id', $lavador)
        //         ->where('detalles.estado_liquidacion', 'Debe')
        //         ->whereBetween('detalles.fecha', [$fecha_ini, $fecha_fin])
        //         ->groupBy('detalles.servicio_id', 'liquidacion_lavadores.tipo_liquidacion', 'liquidacion_lavadores.liquidacion')
        //         // ->get();
        //         ->toSql();
        //         dd($lavador, $fecha_ini, $fecha_fin, $detalles);

        // ******************* ESTE ESTA DANDO ERRORES DE DUPLICIDAD *******************
        //    $detalles =  Detalle::select(
        //                     'detalles.servicio_id',
        //                     DB::raw('SUM(detalles.cantidad) as cantidad'),
        //                     'servicios.precio',
        //                     'servicios.descripcion',
        //                     'servicios.liquidacion as liquidacionServicio',
        //                     'servicios.tipo_liquidacion as tipoLiquidacionServicio',
        //                     'liquidacion_lavadores.tipo_liquidacion as tipoLiquidacionLl',
        //                     'liquidacion_lavadores.liquidacion as liquidacionLl'
        //                 )
        //                 ->join('servicios', 'detalles.servicio_id', '=', 'servicios.id')
        //                 ->leftJoin('liquidacion_lavadores', function($join) {
        //                     $join->on('detalles.servicio_id', '=', 'liquidacion_lavadores.servicio_id')
        //                         ->on('detalles.lavador_id', '=', 'liquidacion_lavadores.lavador_id');
        //                 })
        //                 ->where('detalles.lavador_id', $lavador)
        //                 ->where('detalles.estado_liquidacion', 'Debe')
        //                 ->where('detalles.estado', 'Finalizado')
        //                 ->whereBetween('detalles.fecha', [$fecha_ini, $fecha_fin])
        //                 ->whereNull('detalles.deleted_at')
        //                 ->groupBy('detalles.servicio_id', 'liquidacion_lavadores.tipo_liquidacion', 'liquidacion_lavadores.liquidacion')
        //                 ->get();

        $subquery = DB::table('liquidacion_lavadores as liqui')
                        ->select(DB::raw('MIN(id) as id'), 'servicio_id', 'lavador_id')
                        ->groupBy('servicio_id', 'lavador_id');

        $detalles = DB::table('detalles')
            ->join('servicios', 'detalles.servicio_id', '=', 'servicios.id')
            ->leftJoinSub($subquery, 'll', function ($join) {
                $join->on('detalles.servicio_id', '=', 'll.servicio_id')
                    ->on('detalles.lavador_id', '=', 'll.lavador_id');
            })
            ->leftJoin('liquidacion_lavadores', 'liquidacion_lavadores.id', '=', 'll.id')
            ->where('detalles.lavador_id', $lavador)
            ->where('detalles.estado_liquidacion', 'Debe')
            ->where('detalles.estado', 'Finalizado')
            ->whereBetween('detalles.fecha', [$fecha_ini, $fecha_fin])
            ->whereNull('detalles.deleted_at')
            ->groupBy(
                'detalles.servicio_id',
                'liquidacion_lavadores.tipo_liquidacion',
                'liquidacion_lavadores.liquidacion',
                'servicios.precio',
                'servicios.descripcion',
                'servicios.liquidacion',
                'servicios.tipo_liquidacion'
            )
            ->select(
                'detalles.servicio_id',
                DB::raw('SUM(detalles.cantidad) as cantidad'),
                'servicios.precio',
                'servicios.descripcion',
                'servicios.liquidacion as liquidacionServicio',
                'servicios.tipo_liquidacion as tipoLiquidacionServicio',
                'liquidacion_lavadores.tipo_liquidacion as tipoLiquidacionLl',
                'liquidacion_lavadores.liquidacion as liquidacionLl'
            )
            ->get();

            // ->toSql();
            // dd($detalles, $lavador, $fecha_ini, $fecha_fin);

        return $detalles;

    }
}
