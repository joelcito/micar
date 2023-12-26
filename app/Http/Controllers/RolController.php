<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{

    public function listado(Request $request){
        return view('rol.listado');
    }

    public function guarda(Request $request){

        $data = array();

        if($request->ajax()){

            $rol = new Rol();

            $rol->nombre = $request->input('nombre');
            $rol->descripcion = $request->input('descripcion');

            $rol->save();
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();

        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    public function ajaxListado(Request $request){
        $data = array();

        if($request->ajax()){
            $data['listado']=$this->listadoArray();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    protected function listadoArray(){
        $roles = Rol::all();
        return view('rol.ajaxListado')->with(compact('roles'))->render();
    }

    public function eliminar(Request $request){
        $data = array();
        if($request->ajax()){
            // $rol = Rol::find($request->input('id'));
            Rol::destroy($request->input('id'));

            $data['listado']=$this->listadoArray();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return json_encode($data);
    }

    public function permisos(Request $request){
        if($request->ajax()){

            $rol = Rol::find($request->input('id'));

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

            $rol = Rol::find($request->input('rol_id'));
            $rol->menus = json_encode($data);
            $rol->permisos = json_encode($dataPer);
            $rol->save();

            $data['estado'] = 'success';

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show(Rol $rol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit(Rol $rol)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rol $rol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rol $rol)
    {
        //
    }
}
