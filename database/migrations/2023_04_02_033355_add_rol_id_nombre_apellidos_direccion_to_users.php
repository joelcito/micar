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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('rol_id')->nullable()->after('id');
            $table->foreign('rol_id')->references('id')->on('roles');
            $table->string('nombres')->nullable()->after('name');
            $table->string('ap_paterno')->nullable()->after('nombres');
            $table->string('ap_materno')->nullable()->after('ap_paterno');
            $table->string('cedula')->nullable()->after('ap_materno');
            $table->string('direccion')->nullable()->after('cedula');
            $table->text('menus')->nullable()->after('direccion');
            $table->text('permisos')->nullable()->after('menus');
            $table->string('codigo_punto_venta')->nullable()->after('permisos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);

            $table->dropColumn('rol_id');
            $table->dropColumn('nombres');
            $table->dropColumn('ap_paterno');
            $table->dropColumn('ap_materno');
            $table->dropColumn('cedula');
            $table->dropColumn('direccion');
            $table->dropColumn('menus');
            $table->dropColumn('permisos');
            $table->dropColumn('codigo_punto_venta');
        });
    }
};
