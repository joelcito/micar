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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreign('creador_id')->references('id')->on('users');
            $table->unsignedBigInteger('creador_id')->nullable();

            $table->foreign('modificador_id')->references('id')->on('users');
            $table->unsignedBigInteger('modificador_id')->nullable();

            $table->foreign('eliminador_id')->references('id')->on('users');
            $table->unsignedBigInteger('eliminador_id')->nullable();

            $table->string('nombre')->nullable();
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
        Schema::dropIfExists('categorias');
    }
};
