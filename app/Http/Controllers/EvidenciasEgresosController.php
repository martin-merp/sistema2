<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidenciasEgresos;
use Illuminate\Support\Facades\Auth;

class EvidenciasEgresosController extends Controller
{
    public function index(Request $request)
    {
        // if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $evidencias = EvidenciasEgresos::orderBy('id', 'desc')->paginate(3);
        }
        else{
            $evidencias = EvidenciasEgresos::where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->paginate(3);
        }
        

        return [
            'pagination' => [
                'total'        => $evidencias->total(),
                'current_page' => $evidencias->currentPage(),
                'per_page'     => $evidencias->perPage(),
                'last_page'    => $evidencias->lastPage(),
                'from'         => $evidencias->firstItem(),
                'to'           => $evidencias->lastItem(),
            ],
            'evidencias' => $evidencias
        ];
    }

    public function listarEvidencias(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $evidencias = EvidenciasEgresos::where('id_ingreso','=', $request->id)->paginate(10);

        return [
            'pagination' => [
                'total'        => $evidencias->total(),
                'current_page' => $evidencias->currentPage(),
                'per_page'     => $evidencias->perPage(),
                'last_page'    => $evidencias->lastPage(),
                'from'         => $evidencias->firstItem(),
                'to'           => $evidencias->lastItem(),
            ],
            'evidencias' => $evidencias
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $evidencias = new EvidenciasEgresos();
        $evidencias->nombre = $request->nombre;
        $evidencias->observacion = $request->observacion;
        $evidencias->link = $request->link;
        $evidencias->id_ingreso = $request->id_ingreso;
        $evidencias->usu_crea = $id_usuario;
        $evidencias->id_empresa = $id_empresa;

        $evidencias->save();
    }

    public function eliminarEvidencia(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $evidencias = EvidenciasEgresos::findOrFail($request->id)->delete();
    }
}
