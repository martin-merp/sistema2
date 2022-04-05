<?php

namespace App;

use App\User;
use App\Empresa;
use App\CarpetaProceso;
use Illuminate\Database\Eloquent\Model;

class TipoCarpeta extends Model
{
    protected $table = 'tipos_carpetas';

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function usuCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
    
    public function carpetasProcesos()
    {
        return $this->hasMany(CarpetaProceso::class, 'fk_id_tipos_carpetas');
    }
}
