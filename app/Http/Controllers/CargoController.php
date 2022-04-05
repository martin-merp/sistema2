<?php

namespace App\Http\Controllers;

use App\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CargoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        $respuesta = Cargo::select('id','cargo','descripcion','estado');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        $order = 'id';
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if($buscar){
            $order = 'cargo';
            $where[] = [$criterio, 'LIKE', "%$buscar%"];
        }
        $cargos = $respuesta->where($where)->orderBy($order, 'asc')->paginate(5);


        return [
            'pagination' => [
                'total'        => $cargos->total(),
                'current_page' => $cargos->currentPage(),
                'per_page'     => $cargos->perPage(),
                'last_page'    => $cargos->lastPage(),
                'from'         => $cargos->firstItem(),
                'to'           => $cargos->lastItem(),
            ],
            'cargos' => $cargos
        ];
    }
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $usu = Auth::user()->id;
        $nuevoCargo = $request->all() + [
            'fk_id_empresas' => Session::get('id_empresa'),
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        Cargo::create($nuevoCargo);
    }
    public function update(Request $request, Cargo $cargo)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->cargo != $cargo->cargo) {
            $cargo->cargo = $request->cargo;
        }
        if ($request->descripcion != $cargo->descripcion) {
            $cargo->descripcion = $request->descripcion;
        }
        if ($cargo->isDirty()) {
            $cargo->fk_usuActualiza_users = Auth::user()->id;
            $cargo->save();
        }
    }
    public function desactivar(Request $request, Cargo $cargo)
    {
        if (!$request->ajax()) return redirect('/');
        $cargo->estado = 0;
        $cargo->save();
    }
    public function activar(Request $request, Cargo $cargo)
    {
        if (!$request->ajax()) return redirect('/');
        $cargo->estado = 1;
        $cargo->save();
    }
}
