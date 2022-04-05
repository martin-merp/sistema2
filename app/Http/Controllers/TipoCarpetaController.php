<?php

namespace App\Http\Controllers;

use App\TipoCarpeta;
use Illuminate\Http\Request;

class TipoCarpetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        return ['tiposCarpetas' => TipoCarpeta::select('id', 'tipoCarpeta')->get()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TipoCarpeta  $tipoCarpeta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TipoCarpeta $tipoCarpeta)
    {
        //
    }
}
