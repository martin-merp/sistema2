<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjetivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objetivos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('objetivo', 100);
            $table->text('descripcion');
            $table->enum('clasificacion', ['HSE', 'CALIDAD']);
            $table->unsignedSmallInteger('anio');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_respons_users');
            $table->foreign('fk_respons_users')->references('id')->on('users');
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
        Schema::dropIfExists('objetivos');
    }
}
