<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanesAccionProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planes_accion_proyectos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fk_id_planes_accion');
            #$table->foreign('fk_id_planes_accion')->references('id')->on('planes_accion');
            $table->unsignedInteger('fk_id_empresas');
            #$table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_proyectos');
            #$table->foreign('fk_id_proyectos')->references('id')->on('proyectos');
            $table->unsignedInteger('fk_usuCrea_users');
            #$table->foreign('fk_usuCrea_users')->references('id')->on('users');
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
        Schema::dropIfExists('planes_accion_proyectos');
    }
}
