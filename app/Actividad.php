<?php

namespace App;

use App\User;
use App\PlanAccion;
use App\ConfProyecto;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';
    protected $fillable = [
        'actividad'
        , 'tipoActividad'
        , 'ene'
        , 'feb'
        , 'mar'
        , 'abr'
        , 'may'
        , 'jun'
        , 'jul'
        , 'ago'
        , 'sep'
        , 'oct'
        , 'nov'
        , 'dic'
        , 'diaMesLimite'
        , 'ponderacion'
        , 'fk_id_empresas'
        , 'fk_id_indicadores'
        , 'fk_id_objetivos'
        , 'fk_planAsoc_planes_accion'
        , 'fk_id_planes_accion'
        , 'fk_id_procesos'
        , 'fk_id_proyectos'
        , 'fk_usuCrea_users'
        , 'fk_usuActualiza_users'
    ];

    public function planAccion()
    {
        return $this->belongsTo(PlanAccion::class, 'fk_id_planes_accion');
    }
    public function proyecto()
    {
        return $this->belongsTo(ConfProyecto::class, 'fk_id_proyectos');
    }
    public function responsables()
    {
        return $this->belongsToMany(User::class, 'usuarios_actividades', 'fk_id_actividades', 'fk_id_users');
    }
}
