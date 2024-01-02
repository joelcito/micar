<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'rol_id',
        'nombres',
        'ap_paterno',
        'ap_materno',
        'cedula',
        'direccion',

        'name',
        'email',
        'password',
    ];

    public function rol(){
        return $this->belongsTo('App\Models\Rol', 'rol_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function aperturaCaja(){
        $usuaraio = Auth::user();
        return ($usuaraio->rol_id == 1 || $usuaraio->rol_id == 4)? true : false;
    }

    public function isSupervisor(){
        $usuaraio = Auth::user();
        return ($usuaraio->rol_id == 2)? true : false;
    }

    public function isCajero(){
        $usuaraio = Auth::user();
        return ($usuaraio->rol_id == 4)? true : false;
    }

    public function isAdmin(){
        $usuaraio = Auth::user();
        return ($usuaraio->rol_id == 1)? true : false;
    }

    public function isEdit(){
        $permisos = json_decode(Auth::user()->permisos);
        return ($permisos[0]->estado)? true : false;
    }

    public function isDelete(){
        $permisos = json_decode(Auth::user()->permisos);
        return ($permisos[1]->estado)? true : false;
    }
}
