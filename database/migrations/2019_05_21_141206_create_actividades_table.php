<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('actividad', 50);
            $table->enum('tipoActividad', ['Planear', 'Hacer', 'Verificar', 'Actuar', 'Corregir']);
            $table->boolean('ene')->default(3);
            $table->boolean('feb')->default(3);
            $table->boolean('mar')->default(3);
            $table->boolean('abr')->default(3);
            $table->boolean('may')->default(3);
            $table->boolean('jun')->default(3);
            $table->boolean('jul')->default(3);
            $table->boolean('ago')->default(3);
            $table->boolean('sep')->default(3);
            $table->boolean('oct')->default(3);
            $table->boolean('nov')->default(3);
            $table->boolean('dic')->default(3);
            $table->unsignedTinyInteger('diaMesLimite');
            $table->unsignedSmallInteger('ponderacion');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            #$table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_planAsoc_planes_accion')->nullable();
            #$table->foreign('fk_planAsoc_planes_accion')->references('id')->on('planes_accion');
            $table->unsignedInteger('fk_id_planes_accion');
            #$table->foreign('fk_id_planes_accion')->references('id')->on('planes_accion');
            $table->unsignedInteger('fk_id_procesos');
            #$table->foreign('fk_id_procesos')->references('id')->on('procesos');
            $table->unsignedInteger('fk_id_proyectos');
            #$table->foreign('fk_id_proyectos')->references('id')->on('proyectos');
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
        Schema::dropIfExists('actividades');
    }
}
