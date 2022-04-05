<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndicadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indicadores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('indicador', 100);
            $table->text('descripcion');
            $table->tinyInteger('meta');
            $table->boolean('acumulativo');
            $table->enum('definicion', ['ESTRUCTURA', 'PROCESO', 'RESULTADO']);
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_objetivos');
            $table->foreign('fk_id_objetivos')->references('id')->on('objetivos');
            $table->unsignedInteger('fk_usuCrea_users');
            $table->foreign('fk_usuCrea_users')->references('id')->on('users');
            $table->unsignedInteger('fk_usuActualiza_users');
            $table->foreign('fk_usuActualiza_users')->references('id')->on('users');
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
        Schema::dropIfExists('indicadores');
    }
}
