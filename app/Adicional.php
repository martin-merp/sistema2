<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adicional extends Model
{
    protected $table = 'adicionales_contrato';
    protected $fillable = ['id_contrato','nombre','documento','tipo_documento','parentesco','fecha_nacimiento','usu_crea','usu_edita'];
    public function user()
    {
        return $this->hasOne('App\User');
    }
}
