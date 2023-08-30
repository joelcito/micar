<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreign('creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('creador_id')->nullable();
            $table->foreign('modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('modificador_id')->nullable();
            $table->foreign('eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('eliminador_id')->nullable();
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos');
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('parametro_id')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('carnet')->nullable();
            $table->string('nit')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->decimal('total',12,2)->nullable();
            $table->string('facturado',5)->nullable();
            $table->string('tipo_pago',50)->nullable();
            $table->decimal('monto_pagado',12,2)->nullable();
            $table->decimal('cambio_devuelto',12,2)->nullable();
            $table->string('numero',10)->nullable();
            $table->string('numero_cafc',10)->nullable();
            $table->string('numero_recibo',10)->nullable();
            $table->string('codigo_control',10)->nullable();
            $table->string('cuf')->nullable();
            $table->string('codigo_metodo_pago_siat',10)->nullable();
            $table->decimal('monto_total_subjeto_iva',10,2)->nullable();
            $table->decimal('descuento_adicional',10,2)->nullable();
            $table->text('productos_xml')->nullable();
            $table->string('codigo_descripcion')->nullable();
            $table->string('codigo_recepcion')->nullable();
            $table->string('codigo_trancaccion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('cuis')->nullable();
            $table->string('cufd')->nullable();
            $table->dateTime('fechaVigencia')->nullable();
            $table->string('tipo_factura')->nullable();
            $table->string('uso_cafc')->nullable();
            $table->string('estado_pago',10)->nullable();
            $table->string('estado')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturas');
    }
};
