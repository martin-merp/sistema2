<?php

namespace App\Http\Controllers;

use App\Indicador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IndicadorController extends Controller
{
    /**
     * Display a listing of the resource by objetivo.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPorObjetivo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = Indicador::select('id', 'indicador','descripcion', 'meta', 'acumulativo', 'definicion', 'observaciones', 'estado');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1)$where[] = ['fk_usuCrea_users', '=', Auth::user()->id];

        $where[] = ['fk_id_objetivos', '=', intval($request->fkIdObjetivos)];
        if (isset($request->criValEstadoIndicador))$where[] = ['estado', '=', intval($request->criValEstadoIndicador)];

        $indicadores = $respuesta->where($where)->get();

        return ['indicadores' => $indicadores];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usu = Auth::user()->id;
        $nuevoIndicador = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        Indicador::create($nuevoIndicador);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Indicador  $indicador
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Indicador $indicador)
    {
        if (!$request->ajax()) return redirect('/');

        if ($request->indicador != $indicador->indicador)
            $indicador->indicador = $request->indicador;
        
        if ($request->descripcion != $indicador->descripcion)
            $indicador->descripcion = $request->descripcion;

        if ($request->meta != $indicador->meta)
            $indicador->meta = $request->meta;

        if ($request->acumulativo != $indicador->acumulativo)
            $indicador->acumulativo = $request->acumulativo;

        if ($request->definicion != $indicador->definicion)
            $indicador->definicion = $request->definicion;

        if ($request->observaciones != $indicador->observaciones)
            $indicador->observaciones = $request->observaciones;

        /*if ($request->fkIdObjetivos != $indicador->fk_id_objetivos)
            $indicador->fk_id_objetivos = $request->fkIdObjetivos;*/

        if ($indicador->isDirty()) {
            $indicador->fk_usuActualiza_users = Auth::user()->id;
            $indicador->save();
        }
    }

    public function desactivar(Request $request, Indicador $indicador)
    {
        if (!$request->ajax()) return redirect('/');
        $indicador->estado = 0;
        $indicador->save();
    }
    public function activar(Request $request, Indicador $indicador)
    {
        if (!$request->ajax()) return redirect('/');
        $indicador->estado = 1;
        $indicador->save();
    }
}