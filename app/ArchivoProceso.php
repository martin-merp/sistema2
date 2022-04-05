<?php

namespace App;

use App\User;
use App\Empresa;
use App\CarpetaProceso;
use Illuminate\Database\Eloquent\Model;

class ArchivoProceso extends Model
{
    protected $table = 'archivos_procesos';
    protected $fillable = [
        'archivo', 'codigo', 'versionamiento', 'ruta', 'fk_id_carpetas_procesos', 'fk_id_empresas', 'fk_usuCrea_users', 'fk_usuActualiza_users'
    ];
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function proceso()
    {
        return $this->belongsTo(CarpetaProceso::class, 'fk_id_carpetas_procesos');
    }

    public function usuCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }
}
