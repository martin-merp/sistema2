<?php

namespace App\Http\Controllers;

use App\User;
use App\Proceso;
use App\ConfProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ConfProyectoController extends Controller
{
    public function index(Request $request)
    {
        if(!$request->ajax()) return redirect('/');
        $respuesta = ConfProyecto::join('users','proyectos.fk_respons_users','=','users.id')->select('proyectos.id','proyectos.proyecto','proyectos.descripcion', 'proyectos.correoProductOwner', 'proyectos.fk_respons_users', 'users.usuario', 'proyectos.estado');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['proyectos.fk_usuCrea_users', '=', Auth::user()->id];
        }
        if($request->buscar){
            $where[] = ['proyectos.proyecto', 'LIKE', "%$request->buscar%"];
        }
        if(isset($request->Bestado)){
            $where[] = ['proyectos.estado', '=', intval($request->Bestado)];
        }
        if($request->Bresponsable){
            $where = ['users.usuario', 'LIKE', "%$request->Bresponsable%"];
        }
        if(isset($request->Bparticipantes)){
            $arrIdParticipantes = DB::table('usuarios_proyectos')->where('fk_usuAsoc_users', '=', intval($request->Bparticipantes))->select('fk_id_proyectos')->get();
            $respuesta->whereIn('proyectos.id', $arrIdParticipantes->pluck('fk_id_proyectos'));
        }
        $respuesta->where($where);
        $proyectos = $respuesta->orderBy('proyectos.id', 'desc')->paginate(8);

        return [
            'pagination' => [
                'total'        => $proyectos->total(),
                'current_page' => $proyectos->currentPage(),
                'per_page'     => $proyectos->perPage(),
                'last_page'    => $proyectos->lastPage(),
                'from'         => $proyectos->firstItem(),
                'to'           => $proyectos->lastItem(),
            ],
            'proyectos' => $proyectos,
        ];
    }
    public function selectResponsables(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respon = User::where('condicion', '=', '1')
        ->select('id','nombre_completo')
        ->orderBy('nombre_completo', 'asc')->get();

        return ['responsables' => $respon];
    }

    public function msUsuAsociados(Request $request, $idProyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $usuAsociados = DB::table('usuarios_proyectos')->join('users','usuarios_proyectos.fk_usuAsoc_users','=','users.id')
        ->select('usuarios_proyectos.fk_usuAsoc_users as id','users.nombre_completo')
        ->where('fk_id_proyectos', '=', $idProyecto)
        ->orderBy('id', 'asc')->get();

        return ['msAsociados' => $usuAsociados];
    }
    public function selectProcesos()
    {
        $procesos = Proceso::select('id', 'proceso')->where([
            ['fk_id_empresas', '=', Session::get('id_empresa')],
            ['estado', '=', 1]
        ])->get();
        
        return ['procesos' => $procesos];
    }
    public function msProcAsociados(Request $request, $idProyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $msProcesos = DB::table('procesos_proyectos')->join('procesos','procesos_proyectos.fk_id_procesos','=','procesos.id')
        ->select('procesos_proyectos.fk_id_procesos as id','procesos.proceso')
        ->where('fk_id_proyectos', '=', $idProyecto)
        ->orderBy('id', 'asc')->get();

        return ['msProcesos' => $msProcesos];
    }
    public function listarUsuAsociados(Request $request, $idProyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $respon = DB::table('usuarios_proyectos')->join('users','usuarios_proyectos.fk_usuAsoc_users','=','users.id')
        ->join('proyectos','usuarios_proyectos.fk_id_proyectos','=','proyectos.id')
        ->select('usuarios_proyectos.fk_usuAsoc_users as id','users.nombre_completo')
        ->where('fk_id_proyectos', '=', $idProyecto)
        ->orderBy('id', 'asc')->get();

        return ['asociados' => $respon];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();
        
            $idEmpresa = Session::get('id_empresa');
            $usu = Auth::user()->id;

            $nuevo = $request->all() + [
                'fk_id_empresas' => $idEmpresa,
                'fk_usuCrea_users' => $usu,
                'fk_usuActualiza_users' => $usu,
            ];
            $proyecto = ConfProyecto::create($nuevo);

            $valores = $request->value;
            $nuevosAsociados = [];
            foreach($valores as $valor)
            {
                $nuevosAsociados[$valor['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $proyecto->asociados()->attach($nuevosAsociados);

            $procesos = $request->valueProcesos;
            $nuevosProcesos = [];
            foreach($procesos as $proceso)
            {
                $nuevosProcesos[$proceso['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $proyecto->procesos()->attach($nuevosProcesos);
            
            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }
    public function update(Request $request, ConfProyecto $confProyecto){
        if (!$request->ajax()) return redirect('/');
        try{
            DB::beginTransaction();

            $idEmpresa = Session::get('id_empresa');
            $usu = Auth::user()->id;

            if ($request->proyecto != $confProyecto->proyecto) {
                $confProyecto->proyecto = $request->proyecto;
            }
            if ($confProyecto->descripcion != $request->descripcion) {
                $confProyecto->descripcion = $request->descripcion;
            }
            if ($request->fk_respons_users != $confProyecto->fk_respons_users) {
                $confProyecto->fk_respons_users = $request->fk_respons_users;
            }
            if ($request->correoProductOwner != $confProyecto->correoProductOwner) {
                $confProyecto->correoProductOwner = $request->correoProductOwner;
            }
            if ($confProyecto->isDirty()) {
                $confProyecto->fk_usuActualiza_users = $usu;
                $confProyecto->save();
            }

            $confProyecto->asociados()->detach();
            $confProyecto->procesos()->detach();

            $valores = $request->value;
            $procesos = $request->valueProcesos;

            $asociados = [];
            foreach($valores as $valor)
            {
                $asociados[$valor['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $confProyecto->asociados()->attach($asociados);

            $nuevosProcesos = [];
            foreach($procesos as $proceso)
            {
                $nuevosProcesos[$proceso['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            }
            $confProyecto->procesos()->attach($nuevosProcesos);

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }
    public function desactivar(Request $request, ConfProyecto $confProyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $confProyecto->estado = 0;
        $confProyecto->save();
    }

    public function activar(Request $request,  ConfProyecto $confProyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $confProyecto->estado = 1;
        $confProyecto->save();
    }
    public function finalizar(Request $request,  ConfProyecto $confProyecto)
    {
        if (!$request->ajax()) return redirect('/');
        $confProyecto->estado = 2;
        $confProyecto->save();
    }
    
}
