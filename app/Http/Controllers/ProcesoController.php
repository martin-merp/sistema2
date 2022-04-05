<?php

namespace App\Http\Controllers;

use App\Proceso;
use App\ArchivoProceso;
use App\CarpetaProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        $respuesta = Proceso::select('id', 'proceso', 'tipoProceso', 'observacion', 'estado');
        
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->criValbuscarProceso) {
            $where[] = ['proceso', 'LIKE', "%$request->criValbuscarProceso%"];
        }
        if (isset($request->criValClaveTipoProceso)) {
            $where[] = ['tipoProceso', '=', intval($request->criValClaveTipoProceso)];
        }

        $respuesta->where($where);
        $procesos = $respuesta->orderBy('id', 'desc')->paginate(10);

        return [
            'pagination' => [
                'total'        => $procesos->total(),
                'current_page' => $procesos->currentPage(),
                'per_page'     => $procesos->perPage(),
                'last_page'    => $procesos->lastPage(),
                'from'         => $procesos->firstItem(),
                'to'           => $procesos->lastItem(),
            ],
            'procesos' => $procesos
        ];
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
        $proceso = $request->all() + [
            'fk_id_empresas' => $request->session()->get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];

        Proceso::create($proceso);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Proceso  $proceso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proceso $proceso)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->proceso != $proceso->proceso) {
            $proceso->proceso = $request->proceso;
        }
        if ($request->tipoProceso != $proceso->tipoProceso) {
            $proceso->tipoProceso = $request->tipoProceso;
        }
        if ($request->observacion != $proceso->observacion) {
            $proceso->observacion = $request->observacion;
        }
        if ($proceso->isDirty()) {
            $proceso->fk_usuActualiza_users = Auth::user()->id;
            $proceso->save();
        }
    }

    public function desactivar(Request $request, Proceso $proceso)
    {
        if (!$request->ajax()) return redirect('/');
        $proceso->estado = 0;
        $proceso->save();
    }

    public function activar(Request $request, Proceso $proceso)
    {
        if (!$request->ajax()) return redirect('/');
        $proceso->estado = 1;
        $proceso->save();
    }
}
