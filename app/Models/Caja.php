<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory;
	use SoftDeletes;

    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',
        'total_venta',
        'venta_contado',
        'venta_credito',
        'monto_apertura',
        'monto_cierre',
        'fecha',
        'estado',
        'deleted_at',
    ];
}
