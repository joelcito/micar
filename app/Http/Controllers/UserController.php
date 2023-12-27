<?php

namespace App\Http\Controllers;

use App\Models\Detalle;
use App\Models\LiquidacionLavadorPago;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function listado(Request $request){
        $roles = Rol::all();

        // $data = [
        //     [
        //         'id' => 1,
        //         'name' => 'Administracion',
        //         'estado'=> true,
        //         'children' => [
        //             ['id' => 11, 'name' => 'Usuarios', 'url' => 'user', 'estado'=> true ],
        //             ['id' => 12, 'name' => 'Roles', 'url' => 'rol', 'estado'=> true],
        //             ['id' => 13, 'name' => 'Servicios', 'url' => 'servicio', 'estado'=> true],
        //             ['id' => 14, 'name' => 'Categorias', 'url' => 'categoria', 'estado'=> true],
        //             ['id' => 15, 'name' => 'Punto Venta', 'url' => 'puntoVenta/listado', 'estado'=> true],
        //             ['id' => 16, 'name' => 'Evento Significativo', 'url' => 'eventoSignificativo/listado', 'estado'=> true],
        //             ['id' => 17, 'name' => 'Sincronizacion de Catalogos', 'url' => 'sincronizacionCatalogo/listado', 'estado'=> true],
        //         ]
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'Inventarios',
        //         'estado'=> true,
        //         'children' => [
        //             ['id' => 21, 'name' => 'Productos', 'url' => 'servicio/producto', 'estado'=> true],
        //         ]
        //     ],
        //     [
        //         'id' => 3,
        //         'name' => 'Venta y Facturacion',
        //         'estado'=> true,
        //         'children' => [
        //             ['id' => 31, 'name' => 'Listado de Vehiculos', 'url' => 'vehiculo', 'estado'=> true],
        //             ['id' => 32, 'name' => 'Cuentas por Cobrar', 'url' => 'pago/porcobrar', 'estado'=> true],
        //             ['id' => 33, 'name' => 'Clientes', 'url' => 'cliente', 'estado'=> true],
        //         ]
        //     ],
        //     [
        //         'id' => 4,
        //         'name' => 'Pagos',
        //         'estado'=> true,
        //         'children' => [
        //             ['id' => 41, 'name' => 'Ventas', 'url' => 'pago/listado', 'estado'=> true],
        //             ['id' => 42, 'name' => 'Finanzas', 'url' => 'pago/finanza', 'estado'=> true],
        //             ['id' => 43, 'name' => 'Informe Arqueo', 'url' => 'pago/infomearqueo', 'estado'=> true],
        //             ['id' => 44, 'name' => 'Nueva Liquidacion', 'url' => 'pago/liquidacionNew', 'estado'=> true],
        //         ]
        //     ],
        //     [
        //         'id' => 5,
        //         'name' => 'Reportes',
        //         'estado'=> true,
        //         'children' => [
        //             ['id' => 51, 'name' => 'Listado', 'url' => 'reporte/listado', 'estado'=> true],
        //         ]
        //     ],
        // ];

        // $dataPer = [
        //     [
        //         'id' => 1,
        //         'name' => 'Editar',
        //         'estado'=> true
        //     ],
        //     [
        //         'id' => 2,
        //         'name' => 'Eliminar',
        //         'estado'=> true
        //     ]
        // ];

        // dd(json_encode($data), json_encode($dataPer));

        return view("user.listado")->with(compact('roles'));
    }

    public function ajaxListado(Request $request){
        $data = array();
        if($request->ajax()){
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    protected function listadoArray(){
        $usuarios = User::all();
        return view("user.ajaxListado")->with(compact('usuarios'))->render();
    }

    public function guarda(Request $request){
        $data = array();
        if($request->ajax()){

            $user = new User();

            $user->nombres      = $request->input('nombres');
            $user->ap_paterno   = $request->input('ap_paterno');
            $user->ap_materno   = $request->input('ap_materno');
            $user->name         = $request->input('nombres')." ".$request->input('ap_paterno')." ".$request->input('ap_materno');
            $user->cedula       = $request->input('cedula');
            $user->email        = $request->input('email');
            $user->rol_id       = $request->input('rol_id');
            $user->direccion    = $request->input('direccion');
            $user->password     = Hash::make($request->input('password'));

            $user->save();

            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    public function detalle(Request $request, $usuario_id){

        $servicios  = Servicio::where('tipo_liquidacion', 'depende')->get();
        $usuario    = User::find($usuario_id);

        $liquidaciones = LiquidacionLavadorPago::where('lavador_id_user',$usuario_id)->get();

        $serviciosRealizados = Detalle::where('lavador_id', $usuario_id)
                                        ->orderBy('id', 'desc')
                                        ->get();

        return view('user.detalle')->with(compact('usuario', 'servicios', 'liquidaciones', 'serviciosRealizados'));
    }

    public function cambioPass(Request $request){
        if($request->ajax()){
            $user           = User::find($request->input('user_id_new_pro'));
            $user->password = Hash::make($request->input('pass1'));
            $user->save();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function permisos(Request $request){
        if($request->ajax()){

            $rol = User::find($request->input('id'));

            $data['estado'] = 'success';
            $data['listado'] = view('rol.ajaxRoles')->with(compact('rol'))->render();

        }else{
            $data['estado'] = 'error';
        }

        return  $data;
    }

    public function guardarMenusPermisso(Request $request){
        if($request->ajax()){

            $sw_menu_1 = $request->filled('menu_1');
            $sw_menu_2 = $request->filled('menu_2');
            $sw_menu_3 = $request->filled('menu_3');
            $sw_menu_4 = $request->filled('menu_4');
            $sw_menu_5 = $request->filled('menu_5');

            $data = [
                [
                    'id' => 1,
                    'name' => 'Administracion',
                    'estado'=> $sw_menu_1,
                    'children' => [
                        ['id' => 11, 'name' => 'Usuarios', 'url' => 'user', 'estado'=> ($sw_menu_1)? $request->filled('child_11') : $sw_menu_1 ],
                        ['id' => 12, 'name' => 'Roles', 'url' => 'rol', 'estado'=> ($sw_menu_1)? $request->filled('child_12') : $sw_menu_1],
                        ['id' => 13, 'name' => 'Servicios', 'url' => 'servicio', 'estado'=> ($sw_menu_1)? $request->filled('child_13') : $sw_menu_1],
                        ['id' => 14, 'name' => 'Categorias', 'url' => 'categoria', 'estado'=> ($sw_menu_1)? $request->filled('child_14') : $sw_menu_1],
                        ['id' => 15, 'name' => 'Punto Venta', 'url' => 'puntoVenta/listado', 'estado'=> ($sw_menu_1)? $request->filled('child_15') : $sw_menu_1],
                        ['id' => 16, 'name' => 'Evento Significativo', 'url' => 'eventoSignificativo/listado', 'estado'=> ($sw_menu_1)? $request->filled('child_16') : $sw_menu_1],
                        ['id' => 17, 'name' => 'Sincronizacion de Catalogos', 'url' => 'sincronizacionCatalogo/listado', 'estado'=> ($sw_menu_1)? $request->filled('child_17') : $sw_menu_1],
                    ]
                ],
                [
                    'id' => 2,
                    'name' => 'Inventarios',
                    'estado'=> $sw_menu_2,
                    'children' => [
                        ['id' => 21, 'name' => 'Productos', 'url' => 'servicio/producto', 'estado'=> ($sw_menu_2)? $request->filled('child_21') : $sw_menu_2],
                    ]
                ],
                [
                    'id' => 3,
                    'name' => 'Venta y Facturacion',
                    'estado'=> $sw_menu_3,
                    'children' => [
                        ['id' => 31, 'name' => 'Listado de Vehiculos', 'url' => 'vehiculo', 'estado'=> ($sw_menu_3)? $request->filled('child_31') : $sw_menu_3],
                        ['id' => 32, 'name' => 'Cuentas por Cobrar', 'url' => 'pago/porcobrar', 'estado'=> ($sw_menu_3)? $request->filled('child_32') : $sw_menu_3],
                        ['id' => 33, 'name' => 'Clientes', 'url' => 'cliente', 'estado'=> ($sw_menu_3)? $request->filled('child_33') : $sw_menu_3],
                    ]
                ],
                [
                    'id' => 4,
                    'name' => 'Pagos',
                    'estado'=> $sw_menu_4,
                    'children' => [
                        ['id' => 41, 'name' => 'Ventas', 'url' => 'pago/listado', 'estado'=> ($sw_menu_4)? $request->filled('child_41') : $sw_menu_4],
                        ['id' => 42, 'name' => 'Finanzas', 'url' => 'pago/finanza', 'estado'=> ($sw_menu_4)? $request->filled('child_42') : $sw_menu_4],
                        ['id' => 43, 'name' => 'Informe Arqueo', 'url' => 'pago/infomearqueo', 'estado'=> ($sw_menu_4)? $request->filled('child_43') : $sw_menu_4],
                        ['id' => 44, 'name' => 'Nueva Liquidacion', 'url' => 'pago/liquidacionNew', 'estado'=> ($sw_menu_4)? $request->filled('child_44') : $sw_menu_4],
                    ]
                ],
                [
                    'id' => 5,
                    'name' => 'Reportes',
                    'estado'=> $sw_menu_5,
                    'children' => [
                        ['id' => 51, 'name' => 'Listado', 'url' => 'reporte/listado', 'estado'=> ($sw_menu_5)? $request->filled('child_51') : $sw_menu_5],
                    ]
                ],
            ];

            $dataPer = [
                [
                    'id' => 1,
                    'name' => 'Editar',
                    'estado'=> $request->filled('Editar')
                ],
                [
                    'id' => 2,
                    'name' => 'Eliminar',
                    'estado'=> $request->filled('Eliminar')
                ]
            ];

            $rol = User::find($request->input('rol_id'));
            $rol->menus = json_encode($data);
            $rol->permisos = json_encode($dataPer);
            $rol->save();

            $data['estado'] = 'success';

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }
}
