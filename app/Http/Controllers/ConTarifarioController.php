<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ConTarifario;
use App\ProductoTarifario;
use Illuminate\Support\Facades\Auth;

class ConTarifarioController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $id_empresa = $request->session()->get('id_empresa');
        
        if ($buscar==''){
            $tarifario = ConTarifario::where('id_empresa','=',$id_empresa)->orderBy('id', 'desc')->paginate(3);
        }
        else{
            $tarifario = ConTarifario::where('id_empresa','=',$id_empresa)->where($criterio, 'like', '%'. $buscar . '%')->orderBy('id', 'desc')->paginate(3);
        }
        

        return [
            'pagination' => [
                'total'        => $tarifario->total(),
                'current_page' => $tarifario->currentPage(),
                'per_page'     => $tarifario->perPage(),
                'last_page'    => $tarifario->lastPage(),
                'from'         => $tarifario->firstItem(),
                'to'           => $tarifario->lastItem(),
            ],
            'tarifario' => $tarifario
        ];
    }

    public function selectConTarifario(Request $request){
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');
        $id_producto = $request->id_producto;

        if($id_producto!='')
        {
            $tarifario = ConTarifario::leftJoin('productos_tarifarios','con_tarifarios.id','=','productos_tarifarios.id_tarifario')
            ->select('con_tarifarios.id','con_tarifarios.nombre','productos_tarifarios.valor')
            ->where('productos_tarifarios.id_producto','=',$id_producto)
            ->where('con_tarifarios.id_empresa','=',$id_empresa)
            ->orderBy('nombre', 'asc')
            ->get();
        }
        else
        {
            $tarifario = ConTarifario::where('id_empresa','=',$id_empresa)->orderBy('nombre', 'asc')->get();
        }
        
        return ['tarifario' => $tarifario];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $id_empresa = $request->session()->get('id_empresa');
        $id_usuario = Auth::user()->id;

        $tarifario = new ConTarifario();
        $tarifario->nombre = $request->nombre;
        $tarifario->descripcion = $request->descripcion;
        $tarifario->id_empresa = $id_empresa;
        $tarifario->usu_crea = $id_usuario;
        $tarifario->save();
        
        $arrayProductosTarifario = $request->arrayProductosTarifario;

        foreach($arrayProductosTarifario as $PT)
        {
            $productosTarifario = new ProductoTarifario();
            $productosTarifario->id_tarifario = $tarifario->id;
            $productosTarifario->id_producto = $PT['id_producto'];
            $productosTarifario->valor = $PT['valor'];
            $productosTarifario->save();
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tarifario = ConTarifario::findOrFail($request->id);
        $tarifario->nombre = $request->nombre;
        $tarifario->descripcion = $request->descripcion;
        $tarifario->save();

        $borrarProductosTarifario = ProductoTarifario::where('id_tarifario','=',$tarifario->id)->delete();

        $arrayProductosTarifario = $request->arrayProductosTarifario;

        foreach($arrayProductosTarifario as $PT)
        {
            $productosTarifario = new ProductoTarifario();
            $productosTarifario->id_tarifario = $tarifario->id;
            $productosTarifario->id_producto = $PT['id_producto'];
            $productosTarifario->valor = $PT['valor'];
            $productosTarifario->save();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $tarifario = ConTarifario::findOrFail($request->id);
        $tarifario->estado = '0';
        $tarifario->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $concentracion = ConTarifario::findOrFail($request->id);
        $concentracion->estado = '1';
        $concentracion->save();
    }
}
