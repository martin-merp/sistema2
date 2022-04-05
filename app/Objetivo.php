<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objetivo extends Model
{
    protected $table = 'objetivos';
    protected $fillable = [
        'objetivo', 'descripcion', 'clasificacion', 'anio', 'fk_id_empresas', 'fk_respons_users', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
}
