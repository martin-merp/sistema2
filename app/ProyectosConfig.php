<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProyectosConfig extends Model
{
    protected $table = 'proyectos';
    protected $fillable = ['proyecto','descripcion','estado','responsable','fecha_inicio','fecha_limite','correo-productouner'];

    public function rol(){
        return $this->hasMany('App\UsuariosProyecto');
    }
}
