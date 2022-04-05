<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosActividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_actividades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fk_id_actividades');
            #$table->foreign('fk_id_actividades')->references('id')->on('actividades');
            $table->unsignedInteger('fk_id_empresas');
            #$table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_users');
            #$table->foreign('fk_id_users')->references('id')->on('users');
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
        Schema::dropIfExists('usuarios_actividades');
    }
}
