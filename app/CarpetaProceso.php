<?php

namespace App;

use App\User;
use App\Empresa;
use App\Proceso;
use App\TipoCarpeta;
use App\ArchivoProceso;
use Illuminate\Database\Eloquent\Model;

class CarpetaProceso extends Model
{
    protected $table = 'carpetas_procesos';
    protected $fillable = ['carpeta', 'descripcion', 'fk_id_empresas', 'fk_id_procesos', 'fk_id_tipos_carpetas', 'fk_usuCrea_users', 'fk_usuActualiza_users'];
    
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresas');
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'fk_id_procesos');
    }

    public function tipoCarpeta()
    {
        return $this->belongsTo(TipoCarpeta::class, 'fk_id_tipos_carpetas');
    }

    public function usuCrea()
    {
        return $this->belongsTo(User::class, 'fk_usuCrea_users');
    }

    public function archivosProcesos()
    {
        return $this->hasMany(ArchivoProceso::class, 'fk_id_carpetas_procesos');
    }
}    