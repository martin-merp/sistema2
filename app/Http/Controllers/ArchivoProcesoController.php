<?php

namespace App\Http\Controllers;

use App\ArchivoProceso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ArchivoProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $idEmpresa = Session::get('id_empresa');
        $respuesta = ArchivoProceso::select('id', 'archivo', 'codigo', 'versionamiento', 'ruta', 'descripcion', 'estado');
        $where = [];

        #echo (is_int($request->fkIdCarpetasProcesos)) ? 'ES UN NUMERO': 'ES UNA CADENA';exit; // EL RESULTADO ES CADENA, LOS ATRIBUTOS DE LA PETICION SE ENVIAN COMO CADENAS
        $where[] = ['fk_id_carpetas_procesos', '=', intval($request->fkIdCarpetasProcesos)];
        $where[] = ['fk_id_empresas', '=', $idEmpresa];
        if ($request->leer != 1) {
            $where[] = ['fk_usuCrea_users', '=', Auth::user()->id];
        }
        if ($request->textoBuscar && $request->campo) {
            $where[] = [$request->campo, 'LIKE', "%$request->textoBuscar%"];
        }

        $respuesta->where($where);
        $archivos = $respuesta->orderBy('id', 'desc')->paginate(10);
        $archivos->each(function ($item, $key) {
            $item->ruta = asset("docsProcesos/$item->ruta");
            if (is_null($item->descripcion))$item->descripcion = '';
        });

        return [
            'pagination' => [
                'total'        => $archivos->total(),
                'current_page' => $archivos->currentPage(),
                'per_page'     => $archivos->perPage(),
                'last_page'    => $archivos->lastPage(),
                'from'         => $archivos->firstItem(),
                'to'           => $archivos->lastItem(),
            ],
            'archivos' => $archivos
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
        $idEmpresa = Session::get('id_empresa');
        $carpetaEmpresa = $idEmpresa .'_empresa'; 
        $dirEmpresa = public_path("docsProcesos/$carpetaEmpresa");
        if (!file_exists($dirEmpresa)) mkdir($dirEmpresa, 0777);

        $ruta = $request->ruta->store($carpetaEmpresa, 'docsProcesos');

        $archivo = [
            'archivo' => $request->archivo,
            'codigo' => $request->codigo,
            'versionamiento' => $request->versionamiento,
            'ruta' => $ruta,
            'fk_id_carpetas_procesos' => $request->fk_id_carpetas_procesos,
            'fk_id_empresas' => $idEmpresa,
            'fk_usuCrea_users' => $usu,
            'fk_usuActualiza_users' => $usu,
        ];
        if ($request->descripcion) {
            $archivo['descripcion'] = $request->descripcion;
        }

        ArchivoProceso::create($archivo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ArchivoProceso  $archivoProceso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArchivoProceso $archivoProceso)
    {
        if (!$request->ajax()) return redirect('/');
        if ($request->archivo != $archivoProceso->archivo) {
            $archivoProceso->archivo = $request->archivo;
        }
        if ($request->codigo != $archivoProceso->codigo) {
            $archivoProceso->codigo = $request->codigo;
        }
        if ($request->versionamiento != $archivoProceso->versionamiento) {
            $archivoProceso->versionamiento = $request->versionamiento;
        }
        if ($request->hasFile('ruta')) {
            $carpetaEmpresa = Session::get('id_empresa') .'_empresa';
            $dirEmpresa = public_path("docsProcesos/$carpetaEmpresa");
            Storage::disk('docsProcesos')->delete($archivoProceso->ruta);
            $archivoProceso->ruta = $request->ruta->store($carpetaEmpresa, 'docsProcesos');
        }

        if ($request->descripcion != $archivoProceso->descripcion) {
            $archivoProceso->descripcion = $request->descripcion;
        }

        if ($archivoProceso->isDirty()) {
            $archivoProceso->fk_usuActualiza_users = Auth::user()->id;
            $archivoProceso->save();
        }
    }

    public function desactivar(Request $request, ArchivoProceso $archivoProceso)
    {
        if (!$request->ajax()) return redirect('/');
        $archivoProceso->estado = 0;
        $archivoProceso->save();
    }

    public function activar(Request $request, ArchivoProceso $archivoProceso)
    {
        if (!$request->ajax()) return redirect('/');
        $archivoProceso->estado = 1;
        $archivoProceso->save();
    }
}
