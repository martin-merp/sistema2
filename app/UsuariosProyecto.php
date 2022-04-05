<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsuariosProyecto extends Model
{
    protected $table = 'usuarios_proyecto';
    protected $filliable = ['proyecto_id','usuario_id'];

    public function users()
    {
        return $this->belongsTo('App\User');
    }
    public function proyectos()
    {
        return $this->belongsTo('App\ProyectosConfig');
    }


}
