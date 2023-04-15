<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';

    protected $fillable = [
        // 'creador_id',
        // 'modificador_id',
        // 'eliminador_id',

        'nombre',
        'descripcion',

        'estado',
        'deleted_at',
    ];
}
