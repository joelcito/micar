@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    <h1>aqui lo unevo</h1>


@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        {{--  $( document ).ready(function() {
            ajaxListado();
        });

        function ajaxListado(){
            $.ajax({
                url: "{{ url('categoria/ajaxListado') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_categoria').html(data.listado);
                }
            });
        }

        function guardarCategoria(){
            if($("#formularioCategoria")[0].checkValidity()){
                datos = $("#formularioCategoria").serializeArray()
                $.ajax({
                    url: "{{ url('categoria/guarda') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_categoria').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se cambio  con exito!',
                                timer: 1500
                            })
                            $('#kt_modal_add_categoria').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioCategoria")[0].reportValidity()
            }
        }

        function editarCategoria(categoria, nombre, descripcion){
            $('#nombre').val(nombre);
            $('#descripcion').val(descripcion);
            $('#categoria_id').val(categoria)

            $('#kt_modal_add_categoria').modal('show');

        }

        function nuevoCategoria(){
            $('#nombre').val('');
            $('#descripcion').val('');
            $('#categoria_id').val(0)

            $('#kt_modal_add_categoria').modal('show');
        }

        function eliminrCategoria(categoria){
            Swal.fire({
                title: 'Estas seguro de eliminar la categoria ?',
                text: "No podrás revertir esto.!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('categoria/eliminar') }}",
                        type: 'POST',
                        data:{id:categoria},
                        dataType: 'json',
                        success: function(data) {
                            if(data.estado === 'success'){
                                $('#table_categoria').html(data.listado);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado!',
                                    text: 'La categoria se elimino!',
                                    timer: 1000
                                })
                            }
                        }
                    });
                }
            })
        }  --}}
    </script>
@endsection


