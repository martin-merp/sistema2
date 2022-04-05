<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';
    protected $fillable = [
        'cargo', 'descripcion', 'fk_id_empresas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
    
    /*public function carpetasProcesos()
    {
        return $this->hasMany(CarpetaProceso::class, 'fk_id_procesos');
    }*/
}