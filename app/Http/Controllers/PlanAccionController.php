<?php

namespace App\Http\Controllers;

use App\User;
use App\Empresa;
use App\Proceso;
use App\PlanAccion;
use App\ConfProyecto;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PlanAccionController extends Controller
{
    public function basicoProyectosRegistrados($idPlanAccion)
    {
        $respuesta = DB::table('planes_accion_proyectos')
        ->join('proyectos', 'planes_accion_proyectos.fk_id_proyectos', '=', 'proyectos.id')
        ->where('fk_id_planes_accion', '=', $idPlanAccion)
        ->select('proyectos.id', 'proyectos.proyecto')->get();
        return ['proyectosRegistrados' => $respuesta];
    }
    public function basicoListarProcesos($permisoLeer)
    {
        $respuesta = Proceso::select('id', 'proceso');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($permisoLeer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        $procesos = $respuesta->where($where)->orderBy('proceso', 'asc')->get();
        return ['procesos' => $procesos];
    }
    public function basicoListarProyectos($permisoLeer)
    {
        $respuesta = ConfProyecto::select('id', 'proyecto');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($permisoLeer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        $proyectos = $respuesta->where($where)->orderBy('proyecto', 'asc')->get();
        return ['proyectos' => $proyectos];
    }
    public function basicoListarUsuarios()
    {
        $usuarios = User::select('id', 'nombre_completo')->where('empresas_id', '=', Session::get('id_empresa'))->orderBy('nombre_completo')->get();
        return ['usuarios' => $usuarios];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = PlanAccion::join('users', 'planes_accion.fk_respons_users', '=', 'users.id')
        ->join('procesos', 'planes_accion.fk_id_procesos', '=', 'procesos.id')
        ->select(
            'planes_accion.id'
            , 'planes_accion.planAccion'
            , 'planes_accion.anio'
            , 'planes_accion.puntaje'
            , 'planes_accion.fk_respons_users'
            , 'users.nombre_completo'
            , 'planes_accion.estado'
            , 'planes_accion.fk_id_procesos'
            , 'procesos.proceso'
        );
        
        if($request->criValFkIdProyectos){
            $idsPlanesAccion = DB::table('planes_accion_proyectos')->where('fk_id_proyectos', '=', intval($request->criValFkIdProyectos))->select('fk_id_planes_accion');
            $respuesta->whereIn('planes_accion.id', $idsPlanesAccion->pluck('fk_id_planes_accion'));
        }
        $where = [];
        $where[] = ['planes_accion.fk_id_empresas', '=', Session::get('id_empresa')];
        if ($request->leer != 1) {
            $where[] = ['planes_accion.fk_usuCrea_users', '=', Auth::user()->id];
        }
        if($request->criValFkIdResponsUsers){
            $where[] = ['planes_accion.fk_respons_users', '=', intval($request->criValFkIdResponsUsers)];
        }
        if($request->criValAnio){
            $where[] = ['planes_accion.anio', '=', intval($request->criValAnio)];
        }
        if(isset($request->criValEstado)){
            $where[] = ['planes_accion.estado', '=', intval($request->criValEstado)];
        }
        $where[] = ['planes_accion.fk_id_procesos', '=', intval($request->criValFkIdProcesos)];
        if($request->criValPlanAccion){
            $where[] = ['planes_accion.planAccion', 'LIKE', "%$request->criValPlanAccion%"];
        }
        $planesAccion = $respuesta->where($where)->orderBy('planes_accion.id', 'desc')->paginate(8);
        return [
            'pagination' => [
                'total'        => $planesAccion->total(),
                'current_page' => $planesAccion->currentPage(),
                'per_page'     => $planesAccion->perPage(),
                'last_page'    => $planesAccion->lastPage(),
                'from'         => $planesAccion->firstItem(),
                'to'           => $planesAccion->lastItem(),
            ],
            'planesAccion' => $planesAccion
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
        $idEmpresa = Session::get('id_empresa');
        $usu = Auth::user()->id;
        $nuevoPlanAccion = $request->all() + [
            'fk_id_empresas' => $idEmpresa,
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        $planAccion = PlanAccion::create($nuevoPlanAccion);
        $idsProyectos = Arr::pluck($request->valueProyectos, 'id');

        $nuevosProyectos = [];
        foreach ($idsProyectos as $idProyecto) {
            $nuevosProyectos[$idProyecto] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
        }
        $planAccion->proyectos()->attach($nuevosProyectos);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlanAccion  $planAccion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlanAccion $planAccion)
    {
        if (!$request->ajax()) return redirect('/');
        $idEmpresa = Session::get('id_empresa');
        $usu = Auth::user()->id;

        if ($request->planAccion != $planAccion->planAccion) {
            $planAccion->planAccion = $request->planAccion;
        }
        if ($request->anio != $planAccion->anio) {
            $planAccion->anio = $request->anio;
        }
        if ($request->puntaje != $planAccion->puntaje) {
            $planAccion->puntaje = $request->puntaje;
        }      
        if ($request->fkResponsUsers != $planAccion->fk_respons_users) {
            $planAccion->fk_respons_users = $request->fkResponsUsers;
        }
        if ($request->fkIdProcesos != $planAccion->fk_id_procesos) {
            $planAccion->fk_id_procesos = $request->fkIdProcesos;
        }
        if ($planAccion->isDirty()) {
            $planAccion->fk_usuActualiza_users = $usu;
            $planAccion->save();
        }

        $planAccion->proyectos()->detach();
        $idsProyectos = Arr::pluck($request->valueProyectos, 'id');

        $nuevosProyectos = [];
        foreach ($idsProyectos as $idProyecto) {
            $nuevosProyectos[$idProyecto] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
        }
        $planAccion->proyectos()->attach($nuevosProyectos);
    }

    public function enviarCorreo(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $empresa = Empresa::where('id', '=', Session::get('id_empresa'))->select('nombre', 'correo')->first();
        $nombreEmpresa = strtoupper($empresa->nombre);
        $destinos = $request->destinos;
        $paramsRespons = User::join('personas', 'users.personas_id', '=', 'personas.id')->whereIn('users.id', Arr::pluck($destinos, 'id'))->select('users.id', 'email')->get();
        $correosReg = $correosAct = '';
        foreach ($destinos as $destino) {
            foreach ($paramsRespons as $paramRespon) {
                if ($paramRespon['id'] == $destino['id']) {
                    if ($destino['tipoCorreo'] == "1") {
                        $correosReg .= ', ' .$paramRespon['email'];
                    } else {
                        $correosAct .= ', ' .$paramRespon['email'];
                    }
                }
            }
        }
        $correosReg = ltrim($correosReg, ', ');
        $correosAct = ltrim($correosAct, ', ');
        $plan = strtoupper($request->planAccion);

        ini_set('sendmail_from', $empresa->correo);
        $headers = "Content-Type: text/html; charset=UTF-8";
        if (isset($request->proceso)) {
            $proceso = strtoupper($request->proceso);
            if ($correosReg) {
                $mensajeReg = "$nombreEmpresa te informa que has sido vinculad@ como responsable del plan $plan para el proceso $proceso.";
                mail($correosReg, 'Planes De Accion -' .strtoupper($nombreEmpresa), wordwrap($mensajeReg, 70, "\r\n"), $headers);
            }
            if ($correosAct) {
                $mensajeAct = "$nombreEmpresa te informa que el plan de acción $plan del proceso $proceso ha sido actualizado.";
                mail($correosAct, 'Novedad En Planes De Accion -' .strtoupper($nombreEmpresa), wordwrap($mensajeAct, 70, "\r\n"), $headers);
            }
        } else {
            $actividad = strtoupper($request->actividad);
            $proyecto = strtoupper($request->proyecto);
            if ($correosReg) {
                $mensajeReg = "$nombreEmpresa te informa que has sido vinculad@ como responsable a la actividad $actividad del plan $plan en el proyecto $proyecto";
                mail($correosReg, "Actividades - $nombreEmpresa", wordwrap($mensajeReg, 70, "\r\n"), $headers);
            }
            if ($correosAct) {
                $mensajeAct = "$nombreEmpresa te informa que la actividad $actividad del plan $plan en el proyecto $proyecto ha sido actualizada";
                mail($correosAct, "Novedad En Actividades - $nombreEmpresa", wordwrap($mensajeAct, 70, "\r\n"), $headers);
            }
        }
    }

    public function desactivar(Request $request, PlanAccion $planAccion)
    {
        if (!$request->ajax()) return redirect('/');
        $planAccion->estado = 0;
        $planAccion->save();
    }
    public function activar(Request $request, PlanAccion $planAccion)
    {
        if (!$request->ajax()) return redirect('/');
        $planAccion->estado = 1;
        $planAccion->save();
    }
}
