<?php

namespace App\Http\Controllers;

use App\CarpetaProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CarpetaProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = CarpetaProceso::select(
            'carpetas_procesos.id',
            'carpeta',
            'descripcion',
            'fk_id_tipos_carpetas',
            'tipoCarpeta',
            'carpetas_procesos.estado'
        )->join('tipos_carpetas', 'fk_id_tipos_carpetas', '=', 'tipos_carpetas.id');

        $where = [];
        $where[] = ['carpetas_procesos.fk_id_empresas', '=', Session::get('id_empresa')];
        $where[] = ['fk_id_procesos', '=', $request->fkIdProcesos];
        if ($request->leer != 1) {
            $where[] = ['carpetas_procesos.fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->criValbuscarCarpeta) {
            $where[] = ['carpeta', 'LIKE', "%$request->criValbuscarCarpeta%"];
        }
        if (isset($request->criValFkIdTiposCarpetas)) {
            $where[] = ['fk_id_tipos_carpetas', '=', intval($request->criValFkIdTiposCarpetas)];
        }

        $respuesta->where($where);
        $carpetas = $respuesta->orderBy('carpetas_procesos.id', 'desc')->paginate(10);
        $carpetas->each(function ($item, $key) {
            if (is_null($item->descripcion))$item->descripcion = '';
        });

        return [
            'pagination' => [
                'total'        => $carpetas->total(),
                'current_page' => $carpetas->currentPage(),
                'per_page'     => $carpetas->perPage(),
                'last_page'    => $carpetas->lastPage(),
                'from'         => $carpetas->firstItem(),
                'to'           => $carpetas->lastItem(),
            ],
            'carpetas' => $carpetas
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
        CarpetaProceso::create($proceso);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarpetaProceso  $carpetaProceso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarpetaProceso $carpetaProceso)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->carpeta != $carpetaProceso->carpeta) {
            $carpetaProceso->carpeta = $request->carpeta;
        }
        if ($request->descripcion != $carpetaProceso->descripcion) {
            $carpetaProceso->descripcion = $request->descripcion;
        }
        if ($request->fk_id_tipos_carpetas != $carpetaProceso->fk_id_tipos_carpetas) {
            $carpetaProceso->fk_id_tipos_carpetas = $request->fk_id_tipos_carpetas;
        }
        if ($carpetaProceso->isDirty()) {
            $carpetaProceso->fk_usuActualiza_users = Auth::user()->id;
            $carpetaProceso->save();
        }
    }

    public function desactivar(Request $request, CarpetaProceso $carpetaProceso)
    {
        if (!$request->ajax()) return redirect('/');
        $carpetaProceso->estado = 0;
        $carpetaProceso->save();
    }

    public function activar(Request $request, CarpetaProceso $carpetaProceso)
    {
        if (!$request->ajax()) return redirect('/');
        $carpetaProceso->estado = 1;
        $carpetaProceso->save();
    }
}
