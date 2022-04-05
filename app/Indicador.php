<?php

namespace App;

use App\Actividad;
use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicadores';
    protected $fillable = [
        'indicador', 'descripcion', 'meta', 'acumulativo', 'definicion', 'observaciones', 'fk_id_empresas', 'fk_id_objetivos', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];

    public function actividades()
    {
        return $this->belongsToMany(Actividad::class, 'indicadores_actividades', 'fk_id_indicadores', 'fk_id_actividades');        
    }
}
