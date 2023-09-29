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
        Schema::create('liquidacion_lavador_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreign('creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('creador_id')->nullable();
            $table->foreign('modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('modificador_id')->nullable();
            $table->foreign('eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('eliminador_id')->nullable();

            $table->foreign('lavador_id_user')->references('id')->on('users');
            $table->unsignedBigInteger('lavador_id_user')->nullable();
            $table->foreign('lavador_id_cliente')->references('id')->on('clientes');
            $table->unsignedBigInteger('lavador_id_cliente')->nullable();
            $table->date('fecha_pagado')->nullable();
            $table->decimal('total_servicios', 15, 2)->nullable();
            $table->decimal('cuenta_por_pagar', 15, 2)->nullable();
            $table->decimal('liquido_pagable', 15, 2)->nullable();
            $table->string('detalles_id')->nullable();

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
        Schema::dropIfExists('liquidacion_lavador_pagos');
    }
};
