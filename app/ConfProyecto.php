<?php

namespace App;

use App\User;
use App\Proceso;
use App\PlanAccion;
use Illuminate\Database\Eloquent\Model;

class ConfProyecto extends Model
{
    protected $table = 'proyectos';
    protected $fillable = ['proyecto', 'descripcion', 'correoProductOwner', 'fk_id_empresas', 'fk_respons_users', 'fk_usuCrea_users', 'fk_usuActualiza_users'];

    public function rol(){
        return $this->hasMany('App\UsuariosProyecto');
    }

    public function asociados()
    {
        return $this->belongsToMany(User::class, 'usuarios_proyectos', 'fk_id_proyectos', 'fk_usuAsoc_users');
    }
    public function planesAccion()
    {
        return $this->belongsToMany(PlanAccion::class, 'planes_accion_proyectos', 'fk_id_proyectos', 'fk_id_planes_accion');
    }
    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'procesos_proyectos', 'fk_id_proyectos', 'fk_id_procesos');
    }
}
