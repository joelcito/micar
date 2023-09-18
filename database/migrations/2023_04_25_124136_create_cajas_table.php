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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreign('creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('creador_id')->nullable();
            $table->foreign('modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('modificador_id')->nullable();
            $table->foreign('eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('eliminador_id')->nullable();

            $table->decimal('total_venta',12,2)->nullable();
            $table->decimal('venta_contado',12,2)->nullable();
            $table->decimal('venta_credito',12,2)->nullable();
            $table->decimal('otros_ingresos',12,2)->nullable();
            $table->decimal('total_ingresos',12,2)->nullable();
            $table->decimal('total_qrtramsferencia',12,2)->nullable();
            $table->decimal('total_salidas',12,2)->nullable();
            $table->decimal('total_salidas_gasto',12,2)->nullable();
            $table->decimal('saldo',12,2)->nullable();
            $table->decimal('monto_apertura',12,2)->nullable();
            $table->decimal('monto_cierre',12,2)->nullable();
            $table->datetime('fecha_apertura')->nullable();
            $table->datetime('fecha_cierre')->nullable();
            $table->string('descripcion')->nullable();

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
        Schema::dropIfExists('cajas');
    }
};
