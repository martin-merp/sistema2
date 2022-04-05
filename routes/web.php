<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>['guest']],function(){
    Route::get('/','Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::get('/login', 'Auth\LoginController@login')->name('login');;
});
Route::group(['middleware'=>['auth']],function(){
    
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    
    
    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');

    /*Route::group(['middleware' => ['Almacenero']], function () {
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');

        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

        Route::get('/egreso', 'EgresoController@index');
        Route::post('/egreso/registrar', 'EgresoController@store');
        Route::put('/egreso/desactivar', 'EgresoController@desactivar');
        Route::get('/egreso/obtenerCabecera', 'EgresoController@obtenerCabecera');
        Route::get('/egreso/obtenerDetalles', 'EgresoController@obtenerDetalles');

    });

    Route::group(['middleware' => ['Vendedor']], function () {
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
 
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');

        Route::get('/venta', 'VentaController@index');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/desactivar', 'VentaController@desactivar'); 
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
    });*/

    Route::group(['middleware' => ['Administrador']], function ()  {
        
        //Route::get('/login', 'Auth\LoginController@login');

        // Route::get('/categoria', 'CategoriaController@index');
        // Route::post('/categoria/registrar', 'CategoriaController@store');
        // Route::put('/categoria/actualizar', 'CategoriaController@update');
        // Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        // Route::put('/categoria/activar', 'CategoriaController@activar');
        // Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        /*Route::get('/concentracion', 'ConcentracionController@index');
        Route::post('/concentracion/registrar', 'ConcentracionController@store');
        Route::put('/concentracion/actualizar', 'ConcentracionController@update');
        Route::get('/concentracion/selectConcentracion', 'ConcentracionController@selectConcentracion');*/

        Route::get('/rol', 'RolController@index');
        Route::post('rol/registrar', 'RolController@store');
        Route::put('rol/actualizar', 'RolController@update');
        Route::put('/rol/desactivar', 'RolController@desactivar');
        Route::put('/rol/activar', 'RolController@activar');
        Route::get('/rol/permisos', 'RolController@listarPermisos');
        Route::get('/rol/selectRol', 'RolController@selectRol');

        Route::get('/configgenerales', 'ConfigGeneralesController@index');
        Route::post('configgenerales/registrar', 'ConfigGeneralesController@store');
        Route::post('configgenerales/actualizar', 'ConfigGeneralesController@update');
        Route::put('/configgenerales/desactivar', 'ConfigGeneralesController@desactivar');
        Route::put('/configgenerales/activar', 'ConfigGeneralesController@activar');
        Route::get('/configgenerales/permisos', 'ConfigGeneralesController@listarPermisos');
        Route::get('/configgenerales/selectRol', 'ConfigGeneralesController@selectRol');
        
        Route::get('/modulo', 'ModuloController@index');
        Route::post('/modulo/registrar', 'ModuloController@store');
        Route::put('/modulo/cambiarHijos', 'ModuloController@cambiarHijos');
        Route::get('/modulo/selectPadre', 'ModuloController@selectPadre');
        Route::put('/modulo/actualizar', 'ModuloController@update');
        Route::put('/modulo/desactivar', 'ModuloController@desactivar');
        Route::put('/modulo/desactivarHijos', 'ModuloController@desactivarHijos');
        Route::put('/modulo/activar', 'ModuloController@activar');
        Route::put('/modulo/activarHijos', 'ModuloController@activarHijos');

        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::put('/user/actualizar', 'UserController@update');
        Route::put('/user/desactivar', 'UserController@desactivar');
        Route::put('/user/activar', 'UserController@activar');

        Route::post('/permisos', 'PermisosController@insertar');
        Route::post('/listar_permisos', 'PermisosController@listarPermisos');
        Route::get('/listar_permisos2', 'PermisosController@listarPermisos2');
        Route::get('/permisos/listarPermisosLogueado', 'PermisosController@listarPermisosLogueado');
        Route::get('/permisos/recargarPermisos', 'PermisosController@recargarPermisos');

        // Rutas del plan de cuentas, informe auxiliares, retenciones y de formatos
        Route::get('/info_auxiliares/selectInformes', 'InfAuxiliaresController@selectInformes');
        Route::get('/info_auxiliares/aux_con_saldos', 'InfAuxiliaresController@AuxConSaldos');
        Route::get('/info_auxiliares/aux_con_saldos_exe','InfAuxiliaresController@AuxSaldosExel');
        Route::get('/info_auxiliares', 'InfAuxiliaresController@index');

        Route::get('/retenciones', 'RetencionesController@index');
        Route::post('/retenciones/registrar', 'RetencionesController@store');
        Route::put('/retenciones/actualizar', 'RetencionesController@update');
        Route::get('/retenciones/selectReteInfo','RetencionesController@selectReteInfo');
        Route::put('/retenciones/desactivar', 'RetencionesController@desactivar');
        Route::put('/retenciones/activar', 'RetencionesController@activar');

        Route::get('/planCuentas', 'PlanCuentas@index');
        Route::post('/planCuentas/registrar', 'PlanCuentas@store');
        Route::put('/planCuentas/actualizar', 'PlanCuentas@update');
        Route::get('/planCuentas/selectCuenta', 'PlanCuentas@selectCuenta');
        Route::get('/planCuentas/selectCuenta2', 'PlanCuentas@selectCuenta2');
        Route::get('/planCuentas/selectCuentaInfo', 'PlanCuentas@selectCuentaInfo');
        Route::put('/planCuentas/desactivar', 'PlanCuentas@desactivar');
        Route::put('/planCuentas/activar', 'PlanCuentas@activar');

        Route::get('/conf_formatos', 'Conf_formatosController@index');
        Route::post('/conf_formatos/registrar', 'Conf_formatosController@store');
        Route::put('/conf_formatos/actualizar', 'Conf_formatosController@update');
        Route::get('/conf_formatos/get_tipos_formatos', 'Conf_formatosController@get_tipos_formatos');
        Route::get('/conf_formatos/get_filt_tipo', 'Conf_formatosController@get_filt_tipo');

        Route::get('/formatos','FormatoController@index');
        Route::post('/formatos/registrar','FormatoController@store');
        Route::get('/formatos/numero_next','FormatoController@numero_next');
        Route::put('/formatos/desactivar', 'FormatoController@desactivar');
        Route::put('/formatos/cerrar', 'FormatoController@cerrar');
        Route::get('/formatos/obtenerCabecera', 'FormatoController@obtenerCabecera');
        Route::get('/formatos/obtenerDetalles', 'FormatoController@obtenerDetalles');
        Route::get('/formatos/pdf/{id}','FormatoController@pdf')->name('formato_pdf');

        Route::put('/formatos/actualizar', 'FormatoController@update');

        Route::get('/cuentas/saldo', 'CuentasController@saldo');
        Route::get('/cuentas/get_fuentes', 'CuentasController@get_fuentes');
        Route::get('/cuentas/get_fuentes2', 'CuentasController@get_fuentes2');
        Route::get('/cuentas/get_x_afectar', 'CuentasController@get_x_afectar');

        Route::get('/colaboradores', 'ColaboradoresController@index');
        Route::post('/colaboradores/registrar', 'ColaboradoresController@store');
        Route::put('/colaboradores/actualizar', 'ColaboradoresController@update');
        Route::get('/colaboradores/selectColaborador', 'ColaboradoresController@selectColaborador');
        Route::get('/colaboradores/selectColaboradorVendedor', 'ColaboradoresController@selectColaboradorVendedor');
        Route::put('/colaboradores/desactivar', 'ColaboradoresController@desactivar');
        Route::put('/colaboradores/activar', 'ColaboradoresController@activar');

        Route::get('/zona', 'ZonaController@index');
        Route::post('/zona/registrar', 'ZonaController@store');
        Route::put('/zona/actualizar', 'ZonaController@update');
        Route::get('/zona/selectZona', 'ZonaController@selectZona');
        Route::put('/zona/desactivar', 'ZonaController@desactivar');
        Route::put('/zona/activar', 'ZonaController@activar');

        Route::get('/novedades', 'NovedadesController@index');
        Route::post('/novedades/registrar', 'NovedadesController@store');
        Route::get('/novedades/listarNovedades', 'NovedadesController@listarNovedades');
        Route::put('/novedades/eliminarNovedad', 'NovedadesController@eliminarNovedad');

        Route::get('/evidencias', 'EvidenciasController@index');
        Route::post('/evidencias/registrar', 'EvidenciasController@store');
        Route::get('/evidencias/listarEvidencias', 'EvidenciasController@listarEvidencias');
        Route::put('/evidencias/eliminarEvidencia', 'EvidenciasController@eliminarEvidencia');

        Route::get('/modelo_contable', 'ModeloContableController@index');
        Route::post('/modelo_contable/registrar', 'ModeloContableController@store');
        Route::put('/modelo_contable/actualizar', 'ModeloContableController@update');
        Route::put('/modelo_contable/desactivar', 'ModeloContableController@desactivar');
        Route::put('/modelo_contable/activar', 'ModeloContableController@activar');
        Route::get('/modelo_contable/selectModeloContable', 'ModeloContableController@selectModeloContable');

        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        Route::get('/categoria2', 'CategoriaController2@index');
        Route::post('/categoria2/registrar', 'CategoriaController2@store');
        Route::put('/categoria2/actualizar', 'CategoriaController2@update');
        Route::put('/categoria2/desactivar', 'CategoriaController2@desactivar');
        Route::put('/categoria2/activar', 'CategoriaController2@activar');
        Route::get('/categoria2/selectCategoria', 'CategoriaController2@selectCategoria');

        Route::get('/planCuentasCategorias', 'PlanCuentasCategoriasController@index');
        Route::post('/planCuentasCategorias/registrar', 'PlanCuentasCategoriasController@store');
        Route::put('/planCuentasCategorias/actualizar', 'PlanCuentasCategoriasController@update');
        Route::get('/planCuentasCategorias/selectPlanCuentasCategorias', 'PlanCuentasCategoriasController@selectPlanCuentasCategorias');

        Route::get('/bancos', 'BancosController@index');
        Route::post('/bancos/registrar', 'BancosController@store');
        Route::put('/bancos/actualizar', 'BancosController@update');
        Route::get('/bancos/selectBancos', 'BancosController@selectBancos');
        Route::put('/bancos/desactivar', 'BancosController@desactivar');
        Route::put('/bancos/activar', 'BancosController@activar');

        Route::get('/facturacion', 'FacturacionController@index');
        Route::post('/facturacion/registrar', 'FacturacionController@store');
        Route::put('/facturacion/actualizar', 'FacturacionController@update');
        Route::get('/facturacion/buscarFacturacion', 'FacturacionController@buscarFacturacion');
        Route::put('/facturacion/cambiarEstado', 'FacturacionController@cambiarEstado');
        Route::get('/facturacion/buscarNumFacturaSugerida', 'FacturacionController@buscarNumFacturaSugerida');
        Route::get('/facturacion/obtenerCabecera', 'FacturacionController@obtenerCabecera');

        Route::get('/detalle_facturacion', 'DetalleFacturacionController@index');
        Route::post('/detalle_facturacion/registrar', 'DetalleFacturacionController@store');
        Route::put('/detalle_facturacion/actualizar', 'DetalleFacturacionController@update');
        Route::get('/detalle_facturacion/buscarDetalleFacturacion', 'DetalleFacturacionController@buscarDetalleFacturacion');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');

        Route::get('/presentacion', 'PresentacionController@index');
        Route::post('/presentacion/registrar', 'PresentacionController@store');
        Route::put('/presentacion/actualizar', 'PresentacionController@update');
        Route::get('/presentacion/selectPresentacion', 'PresentacionController@selectPresentacion');
        Route::put('/presentacion/desactivar', 'PresentacionController@desactivar');
        Route::put('/presentacion/activar', 'PresentacionController@activar');

        Route::get('/und_medida', 'UndMedidaController@index');
        Route::post('/und_medida/registrar', 'UndMedidaController@store');
        Route::put('/und_medida/actualizar', 'UndMedidaController@update');
        Route::get('/und_medida/selectUndMedida', 'UndMedidaController@selectUndMedida');
        Route::put('/und_medida/desactivar', 'UndMedidaController@desactivar');
        Route::put('/und_medida/activar', 'UndMedidaController@activar');

        Route::get('/concentracion', 'ConcentracionController@index');
        Route::post('/concentracion/registrar', 'ConcentracionController@store');
        Route::put('/concentracion/actualizar', 'ConcentracionController@update');
        Route::get('/concentracion/selectConcentracion', 'ConcentracionController@selectConcentracion');
        Route::put('/concentracion/desactivar', 'ConcentracionController@desactivar');
        Route::put('/concentracion/activar', 'ConcentracionController@activar');

        Route::get('/stock', 'StockController@index');
        Route::post('/stock/registrar', 'StockController@store');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/actualizar', 'IngresoController@update');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::put('/ingreso/cerrar', 'IngresoController@cerrar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

        Route::get('/detalle_ingreso', 'DetalleIngresoController@index');
        Route::post('/detalle_ingreso/registrar', 'DetalleIngresoController@store');
        Route::put('/detalle_ingreso/actualizar', 'DetalleIngresoController@update');
        Route::get('/detalle_ingreso/selectDetalleIngreso', 'DetalleIngresoController@selectDetalleIngreso');
        Route::put('/detalle_ingreso/desactivar', 'DetalleIngresoController@desactivar');
        Route::put('/detalle_ingreso/activar', 'DetalleIngresoController@activar');

        Route::get('/detalle_egreso', 'DetalleEgresoController@index');
        Route::post('/detalle_egreso/registrar', 'DetalleEgresoController@store');
        Route::put('/detalle_egreso/actualizar', 'DetalleEgresoController@update');
        Route::get('/detalle_egreso/selectDetalleEgreso', 'DetalleEgresoController@selectDetalleEgreso');
        Route::put('/detalle_egreso/desactivar', 'DetalleEgresoController@desactivar');
        Route::put('/detalle_egreso/activar', 'DetalleEgresoController@activar');

        Route::get('/egreso', 'EgresoController@index');
        Route::post('/egreso/registrar', 'EgresoController@store');
        Route::put('/egreso/actualizar', 'EgresoController@update');
        Route::put('/egreso/desactivar', 'EgresoController@desactivar');
        Route::put('/egreso/cerrar', 'EgresoController@cerrar');
        Route::get('/egreso/obtenerCabecera', 'EgresoController@obtenerCabecera');
        Route::get('/egreso/obtenerDetalles', 'EgresoController@obtenerDetalles');

        Route::get('/con_tarifario', 'ConTarifarioController@index');
        Route::post('/con_tarifario/registrar', 'ConTarifarioController@store');
        Route::put('/con_tarifario/actualizar', 'ConTarifarioController@update');
        Route::get('/con_tarifario/selectConTarifario', 'ConTarifarioController@selectConTarifario');
        Route::put('/con_tarifario/desactivar', 'ConTarifarioController@desactivar');
        Route::put('/con_tarifario/activar', 'ConTarifarioController@activar');
        Route::get('/con_tarifario/listarTarifarioPresentacion', 'ConTarifarioController@listarTarifarioPresentacion');

        Route::get('/producto_tarifario', 'ProductoTarifarioController@index');
        Route::post('/producto_tarifario/registrar', 'ProductoTarifarioController@store');
        Route::put('/producto_tarifario/actualizar', 'ProductoTarifarioController@update');
        Route::get('/producto_tarifario/selectProductoTarifario', 'ProductoTarifarioController@selectProductoTarifario');
        Route::put('/producto_tarifario/desactivar', 'ProductoTarifarioController@desactivar');
        Route::put('/producto_tarifario/activar', 'ProductoTarifarioController@activar');

        Route::get('/departamentos', 'DepartamentosController@index');

        Route::get('/municipios', 'MunicipiosController@index');
        Route::get('/municipios/listarMunicipios', 'MunicipiosController@listarMunicipios');

        Route::get('/iva', 'IvaController@index');
        Route::post('/iva/registrar', 'IvaController@store');
        Route::put('/iva/actualizar', 'IvaController@update');
        Route::get('/iva/selectIva', 'IvaController@selectIva');
        Route::put('/iva/desactivar', 'IvaController@desactivar');
        Route::put('/iva/activar', 'IvaController@activar');

        Route::get('/iva_producto', 'IvaProductoController@index');
        Route::post('/iva_producto/registrar', 'IvaProductoController@store');
        Route::put('/iva_producto/actualizar', 'IvaProductoController@update');
        Route::get('/iva_producto/selectIvaProducto', 'IvaProductoController@selectIvaProducto');
        Route::put('/iva_producto/eliminarIvaProducto', 'IvaProductoController@eliminarIvaProducto');

        Route::get('/productos_asociados', 'ProductosAsociadosController@index');
        Route::post('/productos_asociados/registrar', 'ProductosAsociadosController@store');
        Route::put('/productos_asociados/actualizar', 'ProductosAsociadosController@update');
        Route::get('/productos_asociados/selectProductoAsociado', 'ProductosAsociadosController@selectProductoAsociado');
        Route::put('/productos_asociados/desactivar', 'ProductosAsociadosController@desactivar');
        Route::put('/productos_asociados/activar', 'ProductosAsociadosController@activar');

        Route::get('/formato_proceso', 'FormatoProcesoController@index');
        Route::post('/formato_proceso/registrar', 'FormatoProcesoController@store');
        Route::put('/formato_proceso/actualizar', 'FormatoProcesoController@update');
        Route::get('/formato_proceso/selectFormatoProceso', 'FormatoProcesoController@selectFormatoProceso');
        Route::put('/formato_proceso/desactivar', 'FormatoProcesoController@desactivar');
        Route::put('/formato_proceso/activar', 'FormatoProcesoController@activar');

        // Rutas de configuracion del sistema de gestion de riesgo
        Route::get('/procesos', 'ProcesoController@index');
        Route::post('/procesos/registrar', 'ProcesoController@store');
        Route::put('/procesos/actualizar/{proceso}', 'ProcesoController@update');
        Route::patch('/procesos/desactivar/{proceso}', 'ProcesoController@desactivar');
        Route::patch('/procesos/activar/{proceso}', 'ProcesoController@activar');

        Route::get('/tipos_carpetas', 'TipoCarpetaController@index');

        Route::get('/carpetas', 'CarpetaProcesoController@index');
        Route::post('/carpetas/registrar', 'CarpetaProcesoController@store');
        Route::put('/carpetas/actualizar/{carpetaProceso}', 'CarpetaProcesoController@update');
        Route::patch('/carpetas/desactivar/{carpetaProceso}', 'CarpetaProcesoController@desactivar');
        Route::patch('/carpetas/activar/{carpetaProceso}', 'CarpetaProcesoController@activar');

        Route::get('/archivos', 'ArchivoProcesoController@index');
        Route::post('/archivos/registrar', 'ArchivoProcesoController@store');
        Route::put('/archivos/actualizar/{archivoProceso}', 'ArchivoProcesoController@update');
        Route::patch('/archivos/desactivar/{archivoProceso}', 'ArchivoProcesoController@desactivar');
        Route::patch('/archivos/activar/{archivoProceso}', 'ArchivoProcesoController@activar');

        Route::get('/cargos', 'CargoController@index');
        Route::post('/cargos/registrar', 'CargoController@store');
        Route::put('/cargos/actualizar/{cargo}', 'CargoController@update');
        Route::patch('/cargos/desactivar/{cargo}', 'CargoController@desactivar');
        Route::patch('/cargos/activar/{cargo}', 'CargoController@activar');

        Route::get('/conf_proyectos', 'ConfProyectoController@index');
        Route::post('/conf_proyectos/registrar', 'ConfProyectoController@store');
        Route::put('/conf_proyectos/actualizar/{confProyecto}', 'ConfProyectoController@update');
        Route::put('/conf_proyectos/desactivar/{confProyecto}', 'ConfProyectoController@desactivar');
        Route::put('/conf_proyectos/activar/{confProyecto}', 'ConfProyectoController@activar');
        Route::put('/conf_proyectos/finalizar/{confProyecto}', 'ConfProyectoController@finalizar');
        Route::get('/conf_proyectos/s_responsables', 'ConfProyectoController@selectResponsables');
        Route::get('/conf_proyectos/s_procesos', 'ConfProyectoController@selectProcesos');
        Route::get('/conf_proyectos/ms_asociados/{idProyecto}', 'ConfProyectoController@msUsuAsociados');
        Route::get('/conf_proyectos/ms_procesos/{idProyecto}', 'ConfProyectoController@msProcAsociados');
        Route::get('/conf_proyectos/listar_asociados/{idProyecto}', 'ConfProyectoController@listarUsuAsociados');

        Route::get('/planes_accion/proyectos_registrados/{idPlanAccion}', 'PlanAccionController@basicoProyectosRegistrados');
        Route::get('/planes_accion/basico_procesos/{permisoLeer}', 'PlanAccionController@basicoListarProcesos');
        Route::get('/planes_accion/basico_proyectos/{permisoLeer}', 'PlanAccionController@basicoListarProyectos');
        Route::get('/planes_accion/basico_usuarios', 'PlanAccionController@basicoListarUsuarios');
        Route::get('/planes_accion', 'PlanAccionController@index');
        Route::post('/planes_accion/registrar', 'PlanAccionController@store');
        Route::put('/planes_accion/actualizar/{planAccion}', 'PlanAccionController@update');
        Route::post('/planes_accion/enviar_correo', 'PlanAccionController@enviarCorreo');
        Route::patch('/planes_accion/desactivar/{planAccion}', 'PlanAccionController@desactivar');
        Route::patch('/planes_accion/activar/{planAccion}', 'PlanAccionController@activar');

        Route::get('/actividades/planes_accion_asociables', 'ActividadController@basicoListarPlanesAccionAsociables');
        Route::get('/actividades', 'ActividadController@index');
        Route::post('/actividades/registrar', 'ActividadController@store');
        Route::put('/actividades/actualizar/{actividad}', 'ActividadController@update');
        Route::patch('/actividades/desactivar/{actividad}', 'ActividadController@desactivar');
        Route::patch('/actividades/activar/{actividad}', 'ActividadController@activar');

        Route::get('/objetivos/basico_usuarios', 'ObjetivoController@basicoListarUsuarios');
        Route::get('/objetivos', 'ObjetivoController@index');
        Route::post('/objetivos/registrar', 'ObjetivoController@store');
        Route::put('/objetivos/actualizar/{objetivo}', 'ObjetivoController@update');
        Route::patch('/objetivos/desactivar/{objetivo}', 'ObjetivoController@desactivar');
        Route::patch('/objetivos/activar/{objetivo}', 'ObjetivoController@activar');

        Route::get('/indicadores', 'IndicadorController@indexPorObjetivo');
        Route::post('/indicadores/registrar', 'IndicadorController@store');
        Route::put('/indicadores/actualizar/{indicador}', 'IndicadorController@update');
        Route::patch('/indicadores/desactivar/{indicador}', 'IndicadorController@desactivar');
        Route::patch('/indicadores/activar/{indicador}', 'IndicadorController@activar');

        Route::get('/indicadores_actividades/basico_planes_accion/{permisoLeer}', 'IndicadorActividadController@basicoListarPlanesAccion');
        Route::get('/indicadores_actividades', 'IndicadorActividadController@index');
        Route::post('/indicadores_actividades/desatar', 'IndicadorActividadController@desatar');
        Route::post('/indicadores_actividades/atar', 'IndicadorActividadController@atar');
    });

});

//Route::get('/home', 'HomeController@index')->name('home');
