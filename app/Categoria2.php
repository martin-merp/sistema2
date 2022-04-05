<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria2 extends Model
{
    protected $table = 'categorias2';
    //protected $primaryKey = 'id';
    protected $fillable = ['nombre','descripcion','condicion'];

    public function articulos()
    {
        return $this->hasMany('App\Articulo');
    }
}
