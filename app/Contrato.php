<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $table = 'contratos';
    protected $fillable = ['id','n_contrato','id_tercero','lugar_cedula','barrio','tel','cel','id_ocupacion','referencias','tel_referencia','estado','fecha_contrato','hora_contrato','vr_mensual','vr_semestre','vr_año','usu_crea','usu_edita'];

    public function user()
    {
        return $this->hasOne('App\User');
    }
}
