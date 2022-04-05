<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanesAccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planes_accion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('planAccion', 50);
            $table->unsignedSmallInteger('anio');
            $table->unsignedSmallInteger('puntaje');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            #$table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_procesos');
            #$table->foreign('fk_id_procesos')->references('id')->on('procesos');
            $table->unsignedInteger('fk_respons_users');
            #$table->foreign('fk_respons_users')->references('id')->on('users');
            $table->unsignedInteger('fk_usuCrea_users');
            #$table->foreign('fk_usuCrea_users')->references('id')->on('users');
            $table->unsignedInteger('fk_usuActualiza_users');
            #$table->foreign('fk_usuActualiza_users')->references('id')->on('users');
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
        Schema::dropIfExists('planes_accion');
    }
}
