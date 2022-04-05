<?php

namespace App\Http\Controllers;

use App\User;
use App\Empresa;
use App\Actividad;
use App\PlanAccion;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ActividadController extends Controller
{
    /*public function basicoListarUsuarios()
    {
        $usuarios = User::select('id', 'usuarioLargo')->where('empresas_id', '=', Session::get('id_empresa'))->orderBy('usuarioLargo')->get();
        return ['usuarios' => $usuarios];
    }*/

    public function basicoListarPlanesAccionAsociables(Request $request)
    {
        $planesAccionAsoc = PlanAccion::select('id', 'planAccion')->where([
            ['fk_id_empresas', '=', Session::get('id_empresa')]
            , ['fk_id_procesos', '=', $request->idProceso]
            , ['id', '!=', $request->idPlanAccion]
        ])->orderBy('planAccion')->get();

        return ['planesAccionAsoc' => $planesAccionAsoc];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $respuestaActividades = Actividad::leftJoin('planes_accion', 'actividades.fk_planAsoc_planes_accion', '=', 'planes_accion.id')
        ->whereIn('actividades.estado', [0, 1]);
        $where = [
            ['actividades.fk_id_empresas', '=', Session::get('id_empresa')]
            , ['actividades.fk_id_procesos', '=', intval($request->fkIdProcesos)]
            , ['actividades.fk_id_planes_accion', '=', intval($request->fkIdPlanesAccion)]
            , ['actividades.fk_id_proyectos', '=', intval($request->fkIdProyectos)]
        ];
        if ($request->leer != 1) {
            $where[] = ['actividades.fk_usuCrea_users', '=', Auth::user()->id];
        }
        $actividades = $respuestaActividades->where($where)->select(
            'actividades.id'
            , 'actividad'
            , 'tipoActividad'
            , 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'
            , 'diaMesLimite'
            , 'ponderacion'
            , 'actividades.estado'
            , 'fk_id_indicadores'
            , 'fk_planAsoc_planes_accion'
            , 'planes_accion.planAccion'
        )->get();
        
        $respuestaRespons = DB::table('usuarios_actividades')
        ->join('users', 'usuarios_actividades.fk_id_users', '=', 'users.id')
        ->whereIn('usuarios_actividades.fk_id_actividades', $actividades->pluck('id'))
        ->select('usuarios_actividades.fk_id_actividades', 'usuarios_actividades.fk_id_users', 'users.nombre_completo')
        ->orderBy('usuarios_actividades.fk_id_actividades')->orderBy('users.nombre_completo')->get();

        $respuesta = [];
        foreach ($actividades as $actividad) {
            $actividadRespons = [];
            foreach ($respuestaRespons as $respon) {
                if ($respon->fk_id_actividades == $actividad->id) {
                    $actividadRespons[] = ['id' => $respon->fk_id_users, 'nombre_completo' => $respon->nombre_completo];
                }
            }
            
            $respuesta[] = [
                'id' => $actividad->id
                , 'actividad' => $actividad->actividad
                , 'tipoActividad' => $actividad->tipoActividad
                , 'ene' => $actividad->ene
                , 'feb' => $actividad->feb
                , 'mar' => $actividad->mar
                , 'abr' => $actividad->abr
                , 'may' => $actividad->may
                , 'jun' => $actividad->jun
                , 'jul' => $actividad->jul
                , 'ago' => $actividad->ago
                , 'sep' => $actividad->sep
                , 'oct' => $actividad->oct
                , 'nov' => $actividad->nov
                , 'dic' => $actividad->dic
                , 'diaMesLimite' => $actividad->diaMesLimite
                , 'responsables' => $actividadRespons
                , 'ponderacion' => $actividad->ponderacion
                , 'estado' => $actividad->estado
                , 'fk_id_indicadores' => $actividad->fk_id_indicadores
                , 'id_plan_asoc' => $actividad->fk_planAsoc_planes_accion
                , 'planAccionAsociado' => $actividad->planAccion
            ];
        }
        
        return ['actividades' => $respuesta];
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

        $meses = [];
        foreach ($request->arrMesesRegistrados as $mesRegistrado) {
            $meses[$mesRegistrado['id']] = 0;
        }
        $nuevaActividad = $request->only([
            'actividad'
            , 'tipoActividad'
            , 'ponderacion'
            , 'fk_id_indicadores'
            , 'diaMesLimite'
            #, 'fk_planAsoc_planes_accion'
            , 'fk_id_procesos'
            , 'fk_id_proyectos'
            , 'fk_id_planes_accion'
        ]) + $meses + [
            'fk_id_empresas' => $idEmpresa,
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        
        $actividad = Actividad::create($nuevaActividad);

        $idsResponsables = Arr::pluck($request->responsRegistrados, 'id');

        $nuevosResponsables =[];
        foreach ($idsResponsables as $idResponsable) {
            $nuevosResponsables[$idResponsable] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
        }
        $actividad->responsables()->attach($nuevosResponsables);

        /*$responsables = User::join('personas', 'users.id', '=', 'personas.id')
        ->whereIn('users.id', $idsResponsables)
        ->select('users.nombre_completo', 'personas.email AS correo')->get();

        $empresa = Empresa::where('id', '=', $idEmpresa)->select('nombre', 'correo')->first();
        ini_set('sendmail_from', $empresa->correo);
        $descripcion = strtoupper($empresa->nombre). ' te informa que has sido vinculad@ a la actividad ' .strtoupper($actividad->actividad). ' del plan ' .strtoupper($plan). ' en el proyecto ' .strtoupper($proyecto);
        $titulo = 'Actividades - ' .strtoupper($empresa->nombre);
        foreach ($responsables as $responsable) {
            $mensaje = 'Hola ' .ucwords($responsable['nombre_completo']). ",\n\n" . wordwrap($descripcion, 70, "\r\n");
            mail($correoRespons, $titulo, $mensaje);
        }*/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Actividad $actividad)
    {
        if (!$request->ajax()) return redirect('/');
        $idEmpresa = Session::get('id_empresa');
        $usu = Auth::user()->id;

        if ($request->actividad != $actividad->actividad)
            $actividad->actividad = $request->actividad;

        if ($request->tipoActividad != $actividad->tipoActividad)
            $actividad->tipoActividad = $request->tipoActividad;
        
        $arrMeses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $i = 0;
        $mesesElegidos = $request->arrMesesRegistrados;
        $numMesReg = count($mesesElegidos);

        foreach ($arrMeses as $mes) {
            $actividad->{$mes} = 3;
        }
        foreach ($mesesElegidos as $mesElegido) {
            $actividad->{$mesElegido['id']} = 0;
        }
        if ($request->diaMesLimite != $actividad->diaMesLimite)
            $actividad->diaMesLimite = $request->diaMesLimite;
        
        if ($request->ponderacion != $actividad->ponderacion)
            $actividad->ponderacion = $request->ponderacion;

        /*if ($request->fkIdIndicadores != $actividad->fk_id_indicadores)
            $actividad->fk_id_indicadores = $request->fkIdIndicadores;*/
        
        /*if ($request->fkIdObjetivos != $actividad->fk_id_objetivos)
            $actividad->fk_id_objetivos = $request->fkIdObjetivos;*/

        if ($request->fkPlanAsocPlanesAccion != $actividad->fk_planAsoc_planes_accion)
            $actividad->fk_planAsoc_planes_accion = $request->fkPlanAsocPlanesAccion;

        if ($actividad->isDirty()) {
            $actividad->fk_usuActualiza_users = Auth::user()->id;
            $actividad->save();
        }

        $responsExistentes = User::join('personas', 'users.personas_id', '=', 'personas.id')
        ->whereIn('users.id', $actividad->responsables->pluck('id'))
        ->select('users.id', 'users.nombre_completo', 'personas.email')->get();
        
        $idsResponsables = Arr::pluck($request->responsRegistrados, 'id');

        $responsables = User::join('personas', 'users.id', '=', 'personas.id')
        ->whereIn('users.id', $idsResponsables)
        ->select('users.id', 'users.nombre_completo', 'personas.email AS correo')->get();

        $empresa = Empresa::where('id', '=', $idEmpresa)->select('nombre', 'correo')->first();
        ini_set('sendmail_from', $empresa->correo);
        
        $nuevosResponsables = $idsDestinatarios = [];
        foreach ($responsables as $responsable) {
            $nuevosResponsables[$responsable['id']] = ['fk_id_empresas' => $idEmpresa, 'fk_usuCrea_users' => $usu];
            
            /*$alr = TRUE;
            foreach ($responsExistentes as $responExistente) {
                if ($responExistente['id'] == $responsable['id']) {
                    $idsDestinatarios['id'] = $responsable['id'];
                    $idsDestinatarios['accion'] = 1;
                    // AQUI SE LLAMA A MAIL PARA ENVIARLE UNA ALERTA DE QUE LA ACTIVIDAD FUE ACTUALIZADA
                    $titulo = 'Novedad En Actividades - ' .strtoupper($empresa->nombre);
                    $descripcion = strtoupper($empresa->nombre). ' te informa que la actividad ' .strtoupper($actividad->actividad). ' del plan ' .strtoupper($plan). ' en el proyecto ' .strtoupper($proyecto). ' ha sido actualizada';
                    $mensaje = 'Hola ' .ucwords($responsable['nombre_completo']). ",\n\n" . wordwrap($descripcion, 70, "\r\n");
                    mail($responsable['correo'], $titulo, $mensaje);            
                    $alr = FALSE;
                }
            }
            if ($alr) {
                $idsDestinatarios['id'] = $responsable['id'];
                $idsDestinatarios['accion'] = 0;
                // AQUI SE LLAMA A MAIL PARA ENVIARLE UNA ALERTA DE QUE HA SIDO VINCULADO A ESTA ACTIVIDAD
                $titulo = 'Actividades - ' .strtoupper($empresa->nombre);
                $descripcion = strtoupper($empresa->nombre). ' te informa que has sido vinculad@ a la actividad ' .strtoupper($actividad->actividad). ' del plan ' .strtoupper($plan). ' en el proyecto ' .strtoupper($proyecto);
                $mensaje = 'Hola ' .ucwords($responsable['nombre_completo']). ",\n\n" . wordwrap($descripcion, 70, "\r\n");
                mail($responsable['correo'], $titulo, $mensaje);
            }*/
        }
        $actividad->responsables()->detach();
        $actividad->responsables()->attach($nuevosResponsables);

        return ['destinatarios' => $idsDestinatarios];
    }

    public function enviarCorreo(Request $request)
    {
        $responsables = User::join('personas', 'users.id', '=', 'personas.id')
        ->whereIn('users.id', $idsResponsables)
        ->select('users.id', 'users.nombre_completo', 'personas.email AS correo')->get();

        $empresa = Empresa::where('id', '=', $idEmpresa)->select('nombre', 'correo')->first();
    }

    public function desactivar(Request $request, Actividad $actividad)
    {
        if (!$request->ajax()) return redirect('/');
        $actividad->estado = 0;
        $actividad->save();
    }
    public function activar(Request $request, Actividad $actividad)
    {
        if (!$request->ajax()) return redirect('/');
        $actividad->estado = 1;
        $actividad->save();
    }
}
