<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'creador_id',
        'modificador_id',
        'eliminador_id',
        'ap_paterno',
        'ap_materno',
        'nombres',
        'cedula',
        'correo',
        'celular',
        'fecha_nacimiento',
        'estado',
        'deleted_at',
    ];
}
