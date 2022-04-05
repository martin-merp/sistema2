<?php

namespace App;

use App\User;
use App\Proceso;
use App\ConfProyecto;
use Illuminate\Database\Eloquent\Model;

class PlanAccion extends Model
{
    protected $table = 'planes_accion';
    protected $fillable = [
        'planAccion', 'anio', 'puntaje', 'fk_id_empresas', 'fk_id_procesos', 'fk_respons_users', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'fk_id_procesos');
    }
    public function proyectos()
    {
        return $this->belongsToMany(ConfProyecto::class, 'planes_accion_proyectos', 'fk_id_planes_accion', 'fk_id_proyectos');
    }
    public function responsable()
    {
        return $this->belongsTo(User::class, 'fk_respons_users');
    }
}