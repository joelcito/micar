<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoAnulacion extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'motivo_anulaciones';
    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',

        'codigo_sin',
        'nombre',

        'estado',
        'deleted_at',
    ];
}
