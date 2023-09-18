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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreign('creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('creador_id')->nullable();
            $table->foreign('modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('modificador_id')->nullable();
            $table->foreign('eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('eliminador_id')->nullable();
            $table->foreign('factura_id')->references('id')->on('facturas');
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('caja_id')->references('id')->on('cajas');
            $table->unsignedBigInteger('caja_id')->nullable();
            // $table->string('cantidad')->nullable();
            $table->decimal('monto', 15, 2)->nullable();
            $table->datetime('fecha')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('apertura_caja', 3)->nullable();
            $table->string('tipo_pago', 15)->nullable();

            // $table->foreign('creador_id')->references('id')->on('users');
            // $table->unsignedBigInteger('creador_id')->nullable();
            // $table->foreign('modificador_id')->references('id')->on('users');
            // $table->unsignedBigInteger('modificador_id')->nullable();
            // $table->foreign('eliminador_id')->references('id')->on('users');
            // $table->unsignedBigInteger('eliminador_id')->nullable();
            // $table->foreign('factura_id')->references('id')->on('facturas');
            // $table->unsignedBigInteger('factura_id')->nullable();
            // $table->foreign('vehiculo_id')->references('id')->on('vehiculos');
            // $table->unsignedBigInteger('vehiculo_id')->nullable();
            // $table->foreign('servicio_id')->references('id')->on('servicios');
            // $table->unsignedBigInteger('servicio_id')->nullable();
            // $table->foreign('lavador_id')->references('id')->on('users');
            // $table->unsignedBigInteger('lavador_id')->nullable();
            // $table->decimal('precio', 15, 2)->nullable();
            // $table->decimal('cantidad', 15, 2)->nullable();
            // $table->decimal('total',12,2)->nullable();
            // $table->decimal('descuento',12,2)->nullable();
            // $table->decimal('importe',12,2)->nullable();
            // $table->date('fecha')->nullable();
            // $table->string('estado')->nullable();
            // $table->datetime('deleted_at')->nullable();

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
        Schema::dropIfExists('pagos');
    }
};
