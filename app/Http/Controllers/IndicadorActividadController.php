<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Indicador;
use App\PlanAccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IndicadorActividadController extends Controller
{
    public function basicoListarPlanesAccion(Request $request, $permisoLeer)
    {
        if (!$request->ajax()) return redirect('/');
        $respuesta = PlanAccion::select('id', 'planAccion', 'fk_id_procesos');
        $where = [];
        $where[] = ['fk_id_empresas', '=', Session::get('id_empresa')];
        if ($permisoLeer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        $planesAccion = $respuesta->where($where)->orderBy('fk_id_procesos')->orderBy('planAccion')->get();
        $nuevaRespuesta = [];
        foreach ($planesAccion as $planAccion) {
            $nuevaRespuesta[$planAccion['fk_id_procesos']][] = [
                'id' => $planAccion['id']
                , 'planAccion' => $planAccion['planAccion']
            ];
        }
        return ['planesAccion' => $nuevaRespuesta];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $where = [
            ['actividades.fk_id_empresas', '=', Session::get('id_empresa')]
            , ['actividades.fk_id_planes_accion', '=', intval($request->criValFkIdPlanesAccion)]
        ];
        $whereAtados = [['fk_id_empresas', '=', Session::get('id_empresa')]];
        if ($request->leer != 1) {
            $where[] = ['actividades.fk_usuCrea_users', '=', Auth::user()->id];
            $whereAtados[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if (isset($request->criValFkIdProyectos)) {
            $where[] = ['actividades.fk_id_proyectos', '=', intval($request->criValFkIdProyectos)];
        }
        $respuesta = Actividad::join('proyectos', 'fk_id_proyectos', '=', 'proyectos.id')->select('actividades.id', 'actividad', 'proyecto', 'ponderacion');
        $respuesta->where($where);
        if (isset($request->criValColumnaOrden)) {
            $respuesta->orderBy($request->criValColumnaOrden, $request->criValDireccion);
        }
        $actividades = $respuesta->get();

        $actividadesAtadas = DB::table('indicadores_actividades')->select('fk_id_actividades')->where($whereAtados)->get();
        
        $arrAtadas = $arrDesatadas = [];
        foreach ($actividades as $indice => $actividad) {
            $esDesatada = true;
            $arrConIndice = array_merge(['indice' => $indice], $actividad->attributesToArray());
            foreach ($actividadesAtadas as $actividadAtada) {
                if ($actividad->id == $actividadAtada->fk_id_actividades) {
                    $arrAtadas[] = $arrConIndice;
                    $esDesatada = false;
                }
            }
            if ($esDesatada) {
                $arrDesatadas[] = $arrConIndice;
            }
        }
        return ['actividades' => ['atadas' => $arrAtadas, 'desatadas' => $arrDesatadas]];
    }
    public function desatar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $indicador = Indicador::find($request->idIndicador);
        $indicador->actividades()->detach($request->fkIdActividades);
    }
    public function atar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $indicador = Indicador::find($request->idIndicador);
        $indicador->actividades()->attach($request->fkIdActividades, [
            'fk_id_empresas' => Session::get('id_empresa')
            , 'fk_usuCrea_users' => Auth::user()->id
        ]);
    }
}