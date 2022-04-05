<template>
        <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Procesos
                        <button type="button" @click="abrirModal(`${recursoProcesos}Registrar`)" class="btn btn-primary btn-sm" v-if="permisosUser.crear">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" v-model="cadenaConsultaProcesos.criValbuscarProceso" @keyup.enter="cambiarPagina(1, cadenaConsultaProcesos, paginationProcesos, 1)" class="form-control form-control-sm" placeholder="Proceso a buscar">
                                    <select class="form-control form-control-sm col-md-3" v-model="cadenaConsultaProcesos.criValClaveTipoProceso">
                                      <option value="">AMBOS</option>
                                      <option value="1">HSE</option>
                                      <option value="2">CALIDAD</option>
                                    </select>
                                    <button type="submit" @click="cambiarPagina(1, cadenaConsultaProcesos, paginationProcesos, 1)" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Proceso</th>
                                    <th>Tipo proceso</th>
                                    <th>Observacion</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proceso in arrProcesos" :key="proceso.id">
                                    <td v-text="proceso.proceso"></td>
                                    <td v-text="proceso.tipoProceso"></td>
                                    <td v-text="proceso.observacion"></td>
                                    <td>
                                        <div v-if="proceso.estado">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Inactivo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" title="Listar carpetas" @click="abrirModal(`${recursoCarpetas}Listar`, {id : proceso.id, proceso : proceso.proceso})" class="btn btn-info btn-sm" v-if="proceso.estado">
                                          <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                          <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                        </button>&nbsp;
                                        <button type="button" title="Editar" @click="abrirModal(`${recursoProcesos}Actualizar`, proceso)" class="btn btn-warning btn-sm" v-if="proceso.estado && permisosUser.actualizar">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                          <i class="icon-pencil"></i>
                                        </button>&nbsp;
                                        <template v-if="permisosUser.anular">
                                            <button type="button" title="Anular" class="btn btn-danger btn-sm" @click="actualizarEstado(proceso.estado, 'este proceso', recursoProcesos, proceso.id)" v-if="proceso.estado">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" title="Activar" class="btn btn-success btn-sm" @click="actualizarEstado(proceso.estado, 'este proceso', recursoProcesos, proceso.id)" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash" v-if="proceso.estado"></i>
                                                <i class="icon-check" v-else></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="paginationProcesos.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(paginationProcesos.current_page - 1, cadenaConsultaProcesos, paginationProcesos, 1)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumberProcesos" :key="page" :class="[page == paginationProcesos.current_page ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, cadenaConsultaProcesos, paginationProcesos, 1)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="paginationProcesos.current_page < paginationProcesos.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(paginationProcesos.current_page + 1, cadenaConsultaProcesos, paginationProcesos, 1)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label for="text-input">Proceso(*)</label>
                                        <input type="text" v-model="camposBodyProcesos.proceso" class="form-control form-control-sm" placeholder="Nombre del proceso">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="form-control-label form-control-label-sm">Tipo Proceso</label>
                                        <select v-model="camposBodyProcesos.tipoProceso" class="form-control form-control-sm">
                                            <option value="" selected>Seleccione</option>
                                            <option value="1">HSE</option>
                                            <option value="2">CALIDAD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label class="form-control-label form-control-label-sm">Observacion</label>
                                        <textarea v-model="camposBodyProcesos.observacion" class="form-control form-control-sm" rows="3" placeholder="Observacion"></textarea>
                                    </div>
                                </div>
                                <div v-show="alerta" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in msjsAlerta" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion == 1" class="btn btn-primary btn-sm" @click="registrarProceso()">Guardar</button>
                            <button type="button" v-if="tipoAccion == 2" class="btn btn-primary btn-sm" @click="actualizarProceso()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
            <!--Inicio modal carpetas procesos-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalCarpetas}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModalCarpetas()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <i class="fa fa-folder-open-o" aria-hidden="true"></i>&nbsp;Carpetas&nbsp;
                                                <button type="button" @click="abrirModal(`${recursoCarpetas}Registrar`)" class="btn btn-primary btn-sm" v-if="permisosUser.crear">
                                                    <i class="icon-plus"></i>Nueva
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                    <i class="icon-plus"></i>Nueva
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Inicio tabla carpetas -->
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <input type="text" v-model="cadenaConsultaCarpetas.criValbuscarCarpeta" @keyup.enter="cambiarPagina(1, cadenaConsultaCarpetas, paginationCarpetas, 2)" class="form-control form-control-sm col-md-4" placeholder="Carpeta a buscar">
                                                    <select class="form-control form-control-sm col-md-4" v-model="cadenaConsultaCarpetas.criValFkIdTiposCarpetas">
                                                        <option value="" selected>Tipo de carpeta</option>
                                                        <option v-for="tipoCarpeta in arrTiposCarpetas" :value="tipoCarpeta.id" v-text="tipoCarpeta.tipoCarpeta"></option>
                                                    </select>
                                                    <button type="submit" @click="cambiarPagina(1, cadenaConsultaCarpetas, paginationCarpetas, 2)" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Carpeta</th>
                                                    <th>Tipo carpeta</th>
                                                    <th>Estado</th>
                                                    <th>Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="carpeta in arrCarpetas" :key="carpeta.id">
                                                    <td v-text="carpeta.carpeta"></td>
                                                    <td v-text="carpeta.tipoCarpeta"></td>
                                                    <td>
                                                        <div v-if="carpeta.estado">
                                                            <span class="badge badge-success">Activo</span>
                                                        </div>
                                                        <div v-else>
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" title="Listar archivos" @click="abrirModal(`${recursoArchivos}Listar`, {id : carpeta.id, carpeta : carpeta.carpeta})" class="btn btn-info btn-sm" v-if="carpeta.estado">
                                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                            <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                        </button>&nbsp;
                                                        <button type="button" title="Editar" @click="abrirModal(`${recursoCarpetas}Actualizar`, carpeta)" class="btn btn-warning btn-sm" v-if="carpeta.estado && permisosUser.actualizar">
                                                            <i class="icon-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                            <i class="icon-pencil"></i>
                                                        </button>&nbsp;
                                                        <template v-if="permisosUser.anular">
                                                            <button type="button" title="Anular" class="btn btn-danger btn-sm" @click="actualizarEstado(carpeta.estado, 'esta carpeta', recursoCarpetas, carpeta.id)" v-if="carpeta.estado">
                                                                <i class="icon-trash"></i>
                                                            </button>
                                                            <button type="button" title="Activar" class="btn btn-success btn-sm" @click="actualizarEstado(carpeta.estado, 'esta carpeta', recursoCarpetas, carpeta.id)" v-else>
                                                                <i class="icon-check"></i>
                                                            </button>
                                                        </template>
                                                        <template v-else>
                                                            <button type="button" class="btn btn-secondary btn-sm">
                                                                <i class="icon-trash" v-if="carpeta.estado"></i>
                                                                <i class="icon-check"></i>
                                                            </button>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <nav>
                                            <ul class="pagination">
                                                <li class="page-item" v-if="paginationCarpetas.current_page > 1">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(paginationCarpetas.current_page - 1, cadenaConsultaCarpetas, paginationCarpetas, 2)">Ant</a>
                                                </li>
                                                <li class="page-item" v-for="page in pagesNumberCarpetas" :key="page" :class="[page == paginationCarpetas.current_page ? 'active' : '']">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, cadenaConsultaCarpetas, paginationCarpetas, 2)" v-text="page"></a>
                                                </li>
                                                <li class="page-item" v-if="paginationCarpetas.current_page < paginationCarpetas.last_page">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(paginationCarpetas.current_page + 1, cadenaConsultaCarpetas, paginationCarpetas, 2)">Sig</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <!--Fin tabla carpetas-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" @click="cerrarModalCarpetas()">Cerrar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin modal carpetas-->
            <!--Inicio modal carpetas agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalCarpetasModal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalCarpetas"></h4>
                            <button type="button" class="close" @click="cerrarModalCarpetasModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-6">
                                        <label class="form-control-label form-control-label-sm">Carpeta(*)</label>
                                        <input type="text" v-model="camposBodyCarpetas.carpeta" class="form-control form-control-sm" placeholder="Nombre de la carpeta">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-control-label form-control-label-sm">Tipo Carpeta(*)</label>
                                        <select v-model="camposBodyCarpetas.fk_id_tipos_carpetas" class="form-control form-control-sm">
                                            <option value="" selected>Seleccione</option>
                                            <option v-for="tipoCarpeta in arrTiposCarpetas" :value="tipoCarpeta.id" v-text="tipoCarpeta.tipoCarpeta"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label class="form-control-label form-control-label-sm">Descripcion(*)</label>
                                        <textarea class="form-control form-control-sm" v-model="camposBodyCarpetas.descripcion" rows="3"></textarea>
                                    </div>
                                </div>
                                <div v-show="alerta" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in msjsAlerta" :key="error" v-text="error"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" @click="cerrarModalCarpetasModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion == 1" class="btn btn-primary btn-sm" @click="registrarCarpeta()">Guardar</button>
                            <button type="button" v-if="tipoAccion == 2" class="btn btn-primary btn-sm" @click="actualizarCarpeta()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin modal carpetas agregar/actualizar-->
            <!--Inicio modal archivos-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalArchivos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalCarpetas"></h4>
                            <button type="button" class="close" @click="cerrarModalArchivos()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;Archivos&nbsp;
                                                <button type="button" @click="abrirModal(`${recursoArchivos}Registrar`)" class="btn btn-primary btn-sm" v-if="permisosUser.crear">
                                                    <i class="icon-plus"></i>Nuevo
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                    <i class="icon-plus"></i>Nuevo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Inicio tabla carpetas -->
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <select class="form-control form-control-sm col-md-4" v-model="cadenaConsultaArchivos.campo">
                                                    <option value="" disabled>Seleccione</option>
                                                    <option value="archivo">ARCHIVO</option>
                                                    <option value="codigo">CODIGO</option>
                                                    <option value="versionamiento">VERSION</option>
                                                    </select>
                                                    <input type="text" v-model="cadenaConsultaArchivos.textoBuscar" @keyup.enter="cambiarPagina(1, cadenaConsultaArchivos, paginationArchivos, 3)" class="form-control form-control-sm col-md-4" placeholder="Texto a buscar">
                                                    <button type="submit" @click="cambiarPagina(1, cadenaConsultaArchivos, paginationArchivos, 3)" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Buscar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Archivo</th>
                                                    <th>Codigo</th>
                                                    <th>Version</th>
                                                    <th>Estado</th>
                                                    <th>Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="archivo in arrArchivos" :key="archivo.id">
                                                    <td v-text="archivo.archivo"></td>
                                                    <td v-text="archivo.codigo"></td>
                                                    <td v-text="archivo.versionamiento"></td>
                                                    <td>
                                                        <div v-if="archivo.estado">
                                                            <span class="badge badge-success">Activo</span>
                                                        </div>
                                                        <div v-else>
                                                            <span class="badge badge-danger">Inactivo</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a type="button" title="Visualizar" :href="archivo.ruta" target="_blank" class="btn btn-info btn-sm" v-if="archivo.estado"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                        <a type="button" href="#" class="btn btn-secondary btn-sm" v-else><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                        </button>&nbsp;
                                                        <button type="button" title="Editar" @click="abrirModal(`${recursoArchivos}Actualizar`, archivo)" class="btn btn-warning btn-sm" v-if="archivo.estado && permisosUser.actualizar">
                                                            <i class="icon-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                            <i class="icon-pencil"></i>
                                                        </button>&nbsp;
                                                        <template v-if="permisosUser.anular">
                                                            <button type="button" title="Anular" class="btn btn-danger btn-sm" @click="actualizarEstado(archivo.estado, 'este archivo', recursoArchivos, archivo.id)" v-if="archivo.estado">
                                                                <i class="icon-trash"></i>
                                                            </button>
                                                            <button type="button" title="Activar" class="btn btn-success btn-sm" @click="actualizarEstado(archivo.estado, 'este archivo', recursoArchivos, archivo.id)" v-else>
                                                                <i class="icon-check"></i>
                                                            </button>
                                                        </template>
                                                        <template v-else>
                                                            <button type="button" class="btn btn-secondary btn-sm">
                                                                <i class="icon-trash" v-if="archivo.estado"></i>
                                                                <i class="icon-check" v-else></i>
                                                            </button>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <nav>
                                            <ul class="pagination">
                                                <li class="page-item" v-if="paginationArchivos.current_page > 1">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(paginationArchivos.current_page - 1, cadenaConsultaArchivos, paginationArchivos, 3)">Ant</a>
                                                </li>
                                                <li class="page-item" v-for="page in pagesNumberArchivos" :key="page" :class="[page == paginationArchivos.current_page ? 'active' : '']">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, cadenaConsultaArchivos, paginationArchivos, 3)" v-text="page"></a>
                                                </li>
                                                <li class="page-item" v-if="paginationArchivos.current_page < paginationArchivos.last_page">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(paginationArchivos.current_page + 1, cadenaConsultaArchivos, paginationArchivos, 3)">Sig</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <!--Fin tabla carpetas-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" @click="cerrarModalArchivos()">Cerrar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin modal archivos-->
            <!--Inicio modal archivos agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalArchivosModal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalArchivos"></h4>
                            <button type="button" class="close" @click="cerrarModalArchivosModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label class="form-control-label form-control-label-sm">Archivo(*)</label>
                                        <input type="text" v-model="camposBodyArchivos.archivo" class="form-control form-control-sm" placeholder="Nombre del archivo">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="form-control-label form-control-label-sm">Codigo(*)</label>
                                        <input type="text" v-model="camposBodyArchivos.codigo" class="form-control form-control-sm" >
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label for="inputArchivo" class="form-control-label form-control-label-sm">Subir archivo</label>
                                        <input type="file" class="form-control-file form-control-sm" id="inputArchivo"  @change="obtenerArchivo">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label class="form-control-label form-control-label-sm">Version(*)</label>
                                        <input type="text" v-model="camposBodyArchivos.versionamiento" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label class="form-control-label form-control-label-sm">Descripcion</label>
                                        <textarea v-model="camposBodyArchivos.descripcion" class="form-control form-control-sm" rows="3"></textarea>
                                    </div>
                                </div>
                                <div v-show="alerta" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in msjsAlerta" :key="error" v-text="error"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" @click="cerrarModalArchivosModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion == 1" class="btn btn-primary btn-sm" @click="registrarArchivo()">Guardar</button>
                            <button type="button" v-if="tipoAccion == 2" class="btn btn-primary btn-sm" @click="actualizarArchivo()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin modal archivos agregar/actualizar-->
        </main>
</template>
<script>
    export default {
        props : ['permisosUser', 'ruta', 'axiosApp']
        , data ()
        {
            return {
                recursoProcesos : 'procesos'
                , recursoCarpetas : 'carpetas'
                , recursoArchivos : 'archivos'
                
                , cadenaConsultaProcesos : {
                    '?page' : 1
                    , leer : this.permisosUser.leer
                    , criValbuscarProceso : ''
                    , criValClaveTipoProceso : ''
                }
                , arrProcesos : []
                , camposBodyProcesos : {
                  proceso : ''  
                  , tipoProceso : ''
                  , observacion : ''
                }
                , idProceso : 0

                , modal : 0
                , tituloModal : ''

                , cadenaConsultaCarpetas : {
                    '?page' : 1
                    , fkIdProcesos : 0
                    , leer : this.permisosUser.leer
                    , criValbuscarCarpeta : ''
                    , criValFkIdTiposCarpetas : ''
                }
                , arrTiposCarpetas : []
                , arrCarpetas :  []
                , camposBodyCarpetas : {
                    carpeta : ''
                    , descripcion : ''
                    , fk_id_procesos : 0
                    , fk_id_tipos_carpetas : ''
                }
                , idCarpeta : 0

                , modalCarpetas : 0
                , modalCarpetasModal : 0
                , tituloModalCarpetas : ''

                , cadenaConsultaArchivos : {
                    '?page' : 1
                    , fkIdCarpetasProcesos : 0
                    , leer : this.permisosUser.leer
                    , campo : ''
                    , textoBuscar : ''  
                }
                , arrArchivos : []
                , camposBodyArchivos : {
                    archivo : ''
                    , codigo : ''
                    , descripcion : ''
                    , versionamiento : ''
                    , ruta : ''
                    ,fk_id_carpetas_procesos : 0
                }
                , idArchivo : 0
                
                , modalArchivos : 0
                , modalArchivosModal : 0
                , tituloModalArchivos : ''
                
                , tipoAccion : 0
                , alerta : 0
                , msjsAlerta : []

                , paginationProcesos : {
                    'total' : 0
                    , 'current_page' : 0
                    , 'per_page' : 0
                    , 'last_page' : 0
                    , 'from' : 0
                    , 'to' : 0
                }
                , paginationCarpetas : {
                    'total' : 0
                    , 'current_page' : 0
                    , 'per_page' : 0
                    , 'last_page' : 0
                    , 'from' : 0
                    , 'to' : 0
                }
                , paginationArchivos : {
                    'total' : 0
                    , 'current_page' : 0
                    , 'per_page' : 0
                    , 'last_page' : 0
                    , 'from' : 0
                    , 'to' : 0
                }
                , offset : 3
            }
        }
        , computed :
        {
            /*isActived: function (refPagination)
            {
                return refPagination.current_page;
            },*/
            //Calcula los elementos de la paginación
            pagesNumberProcesos: function ()
            {
               if (!this.paginationProcesos.to) {
                    return [];
                }
                var from = this.paginationProcesos.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if (to >= this.paginationProcesos.last_page) {
                    to = this.paginationProcesos.last_page;
                }  
                var pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , pagesNumberCarpetas: function ()
            {
               if (!this.paginationCarpetas.to) {
                    return [];
                }
                var from = this.paginationCarpetas.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if (to >= this.paginationCarpetas.last_page) {
                    to = this.paginationCarpetas.last_page;
                }  
                var pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , pagesNumberArchivos: function ()
            {
               if (!this.paginationArchivos.to) {
                    return [];
                }
                var from = this.paginationArchivos.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if (to >= this.paginationArchivos.last_page) {
                    to = this.paginationArchivos.last_page;
                }  
                var pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        }
        , methods :
        {
            abrirModal (recursoAccion, data = [])
            {
                switch (recursoAccion) {
                    case 'procesosRegistrar':
                        this.modal = 1;
                        this.tituloModal = 'Registrar proceso';
                        this.tipoAccion = 1;
                        break;
                    case 'procesosActualizar':
                        this.idProceso = data.id;
                        this.camposBodyProcesos.proceso = data.proceso;
                        this.camposBodyProcesos.tipoProceso = data.tipoProceso == 'HSE' ? 1 : 2;
                        this.camposBodyProcesos.observacion = data.observacion;
                        this.modal = 1;
                        this.tituloModal = 'Actualizar proceso';
                        this.tipoAccion = 2;
                        break;
                    case 'carpetasListar':
                        this.tituloModal = data.proceso;
                        this.cadenaConsultaCarpetas.fkIdProcesos = this.camposBodyCarpetas.fk_id_procesos = data.id
                        this.selectTiposCarpetas();
                        this.listarCarpetas();
                        this.modalCarpetas = 1;
                        break;
                    case 'carpetasRegistrar':
                        this.modalCarpetasModal = 1;
                        this.tituloModalCarpetas = 'Registrar carpeta';
                        this.tipoAccion = 1;
                        break;
                    case 'carpetasActualizar':
                        this.idCarpeta = data.id;
                        this.camposBodyCarpetas.carpeta = data.carpeta;
                        this.camposBodyCarpetas.descripcion = data.descripcion;
                        this.camposBodyCarpetas.fk_id_tipos_carpetas = data.fk_id_tipos_carpetas;
                        this.modalCarpetasModal = 1;
                        this.tituloModalCarpetas = 'Actualizar carpeta';
                        this.tipoAccion = 2;
                        break;
                    case 'archivosListar':
                        this.cadenaConsultaArchivos.fkIdCarpetasProcesos = this.camposBodyArchivos.fk_id_carpetas_procesos = data.id;
                        this.tituloModalCarpetas = data.carpeta;
                        this.listarArchivos();
                        this.modalArchivos = 1;
                        break;
                    case 'archivosRegistrar':
                        this.modalArchivosModal = 1;
                        this.tituloModalArchivos = 'Registrar archivo';
                        this.tipoAccion = 1;
                        break;
                    case 'archivosActualizar':
                        this.modalArchivosModal = 1;
                        this.tituloModalArchivos = 'Actualizar archivo';
                        this.idArchivo = data.id;
                        this.camposBodyArchivos.archivo = data.archivo;
                        this.camposBodyArchivos.codigo = data.codigo;
                        this.camposBodyArchivos.versionamiento = data.versionamiento;
                        this.camposBodyArchivos.descripcion = data.descripcion;
                        this.camposBodyArchivos.ruta = data.ruta;
                        this.tipoAccion = 2;
                        break;
                }
            }
            , cambiarPagina (page, refCadenaConsulta, refPagination, listar)
            {
                //Actualiza la página actual
                refCadenaConsulta['?page'] = refPagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                switch (listar) {
                    case 1: this.listarProcesos();break;
                    case 2: this.listarCarpetas();break;
                    case 3: this.listarArchivos();break;
                }
            }
            , obtenerRespuesta(uri, peticion)
            {
                var me = this;
                peticion.then(function (response) {
                    if (uri.endsWith('desactivar/') || uri.endsWith('activar/'))swal(me.tituloSwal, me.descripcionSwal, 'success');
                    switch (uri) {
                        case 'procesos':
                            me.arrProcesos = response.data.procesos.data;
                            me.paginationProcesos = response.data.pagination;
                            break;
                        case 'procesos/registrar':
                        case 'procesos/actualizar/':
                            me.cerrarModal();
                            me.listarProcesos();
                            break;
                        case '/tipos_carpetas':
                            me.arrTiposCarpetas = response.data.tiposCarpetas;
                            break;
                        case 'carpetas':
                            me.arrCarpetas = response.data.carpetas.data;
                            me.paginationCarpetas = response.data.pagination;
                            break;
                        case 'carpetas/registrar':
                        case 'carpetas/actualizar/':
                            me.cerrarModalCarpetasModal();
                            me.listarCarpetas();
                            break;
                        case 'archivos':
                            me.arrArchivos = response.data.archivos.data;
                            me.paginationArchivos = response.data.pagination;
                            break;
                        case 'procesos/desactivar/':
                        case 'procesos/activar/':
                            me.listarProcesos();
                            break;
                        case 'carpetas/desactivar/':
                        case 'carpetas/activar/':
                            me.listarCarpetas();
                            break;
                        case 'archivos/desactivar/':
                        case 'archivos/activar/':
                            me.listarArchivos();
                            break;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , esRecursoInvalido (validar) {
                switch (validar) {
                    case 1:if (this.validarProceso())return true;
                        break;
                    case 2: if (this.validarCarpeta())return true;
                        break;
                    case 3: if (this.validarArchivo())return true;
                        break;
                }
                return false;
            }
            , peticionGet (uri, cadenaConsulta = '')
            {
                var peticion = this.axiosApp.get(`${uri}${cadenaConsulta}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPost (uri, camposBody, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                var peticion = this.axiosApp.post(uri, camposBody);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPut (uri, idRecurso, camposBody, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                var peticion = this.axiosApp.put(`${uri}${idRecurso}`, camposBody);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPatch (uri, idRecurso ,estado)
            {
                this.tituloSwal = 'Activado';
                this.descripcionSwal = 'El registro ha sido activado con éxito.';
                if (estado) {
                    this.tituloSwal = 'Desactivado';
                    this.descripcionSwal = 'El registro ha sido desactivado con éxito.';
                }
                var peticion = this.axiosApp.patch(`${uri}${idRecurso}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , listarProcesos () {
                var cadenaConsultaProcesos = Object.entries(this.cadenaConsultaProcesos).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
                this.peticionGet(this.recursoProcesos, cadenaConsultaProcesos);
            }
            , validarProceso ()
            {
                this.msjsAlerta = [];
                if (!this.camposBodyProcesos.proceso) this.msjsAlerta.push("El proceso no puede estar vacío.");
                if (!this.camposBodyProcesos.tipoProceso) this.msjsAlerta.push("Debe seleccionar un tipo proceso.");
                if (this.msjsAlerta.length) this.alerta = 1;
                return this.alerta;
            }
            , registrarProceso ()
            {
                var uri = `${this.recursoProcesos}/registrar`;
                this.peticionPost(uri, this.camposBodyProcesos, 1);
            }
            , actualizarProceso ()
            {
                var uri = `${this.recursoProcesos}/actualizar/`;
                this.peticionPut(uri, this.idProceso, this.camposBodyProcesos, 1);
            }
            , cerrarModal ()
            {
                this.modal = 0;
                this.tituloModal = '';
                this.camposBodyProcesos.proceso = '';
                this.camposBodyProcesos.tipoProceso = '';
                this.camposBodyProcesos.observacion = '';
                this.idProceso = 0;
                this.alerta = 0;
            }
            , selectTiposCarpetas ()
            {
                var uri = '/tipos_carpetas';
                this.peticionGet(uri);
            }
            , listarCarpetas ()
            {
                var cadenaConsultaCarpetas = Object.entries(this.cadenaConsultaCarpetas).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
                this.peticionGet(this.recursoCarpetas, cadenaConsultaCarpetas);
            }
            , validarCarpeta ()
            {
                this.msjsAlerta = [];
                if (!this.camposBodyCarpetas.carpeta) this.msjsAlerta.push("El campo carpeta no puede estar vacío.");
                if (!this.camposBodyCarpetas.fk_id_tipos_carpetas) this.msjsAlerta.push("Debe seleccionar un tipo de carpeta.");
                if (this.msjsAlerta.length) this.alerta = 1;

                return this.alerta;
            }
            , registrarCarpeta ()
            {
                var uri = `${this.recursoCarpetas}/registrar`;
                this.peticionPost(uri, this.camposBodyCarpetas, 2);
            }
            , actualizarCarpeta ()
            {
                var uri = `${this.recursoCarpetas}/actualizar/`;
                this.peticionPut(uri, this.idCarpeta, this.camposBodyCarpetas, 2);
            }
            , cerrarModalCarpetas ()
            {
                this.modalCarpetas = this.cadenaConsultaCarpetas.fkIdProcesos = this.camposBodyCarpetas.fk_id_procesos = this.idProceso = 0;
                this.cadenaConsultaCarpetas.criValbuscarCarpeta = this.cadenaConsultaCarpetas.criValFkIdTiposCarpetas = this.tituloModal = '';
                this.arrCarpetas = [];
            }
            , cerrarModalCarpetasModal ()
            {
                this.modalCarpetasModal = this.alerta = 0;
                this.camposBodyCarpetas.carpeta = this.camposBodyCarpetas.descripcion = this.camposBodyCarpetas.fk_id_tipos_carpetas = this.tituloModalCarpetas = '';
            }
            , listarArchivos ()
            {
                var cadenaConsultaArchivos = Object.entries(this.cadenaConsultaArchivos).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
                this.peticionGet(this.recursoArchivos, cadenaConsultaArchivos);
            }
            , validarArchivo ()
            {
                this.alerta = 0;
                this.msjsAlerta = [];
                if (!this.camposBodyArchivos.archivo) this.msjsAlerta.push("El campo archivo no puede estar vacío.");
                if (!this.camposBodyArchivos.codigo) this.msjsAlerta.push("El campo codigo no puede estar vacío.");
                if (!this.camposBodyArchivos.versionamiento) this.msjsAlerta.push("El campo version no puede estar vacío.");
                if (this.tipoAccion == 1) {
                    if (!this.camposBodyArchivos.ruta) {
                        this.msjsAlerta.push("Debe seleccionar un archivo.");
                    } else {
                        if (this.camposBodyArchivos.ruta.type != 'application/pdf') this.msjsAlerta.push("El archivo debe estar en formato pdf.");
                    }
                }
                if (this.msjsAlerta.length) this.alerta = 1;
                return this.alerta;
            }
            , obtenerArchivo (event)
            {
                this.camposBodyArchivos.ruta = event.target.files[0];
            }
            , registrarArchivo ()
            {
                if (this.validarArchivo())return;
                let me = this;
                var camposBodyArchivos = new FormData();
                camposBodyArchivos.append('archivo', this.camposBodyArchivos.archivo);
                camposBodyArchivos.append('codigo', this.camposBodyArchivos.codigo);
                camposBodyArchivos.append('versionamiento', this.camposBodyArchivos.versionamiento);
                camposBodyArchivos.append('ruta', this.camposBodyArchivos.ruta);
                camposBodyArchivos.append('descripcion', this.camposBodyArchivos.descripcion);
                camposBodyArchivos.append('fkIdCarpetasProcesos', this.camposBodyArchivos.fk_id_carpetas_procesos);                
                axios.post(`${this.recursoArchivos}/registrar`, camposBodyArchivos, {headers:{'Content-Type':'multipart/form-data'}}
                ).then(function (response) {
                    me.cerrarModalArchivosModal();
                    me.listarArchivos();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , actualizarArchivo ()
            {
                if (this.validarArchivo())return;
                let me = this;
                var camposBodyArchivos = new FormData();
                camposBodyArchivos.append('archivo', this.camposBodyArchivos.archivo);
                camposBodyArchivos.append('codigo', this.camposBodyArchivos.codigo);
                camposBodyArchivos.append('versionamiento', this.camposBodyArchivos.versionamiento);
                camposBodyArchivos.append('ruta', this.camposBodyArchivos.ruta);
                camposBodyArchivos.append('descripcion', this.camposBodyArchivos.descripcion);
                camposBodyArchivos.append('_method', 'PUT');
                axios.post(`${this.recursoArchivos}/actualizar/${this.idArchivo}`, camposBodyArchivos, {headers:{'Content-Type':'multipart/form-data'}}
                ).then(function (response) {
                    me.cerrarModalArchivosModal();
                    me.listarArchivos();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , cerrarModalArchivos ()
            {
                this.modalArchivos = this.cadenaConsultaArchivos.fkIdCarpetasProcesos = this.camposBodyArchivos.fk_id_carpetas_procesos = this.idCarpeta= 0;
                this.tituloModalCarpetas = this.cadenaConsultaArchivos.campo = this.cadenaConsultaArchivos.textoBuscar = '';
                this.arrArchivos = [];
            }
            , cerrarModalArchivosModal ()
            {
                this.modalArchivosModal = this.alerta = 0;
                this.camposBodyArchivos.archivo = this.camposBodyArchivos.codigo = this.camposBodyArchivos.versionamiento = this.camposBodyArchivos.ruta = this.camposBodyArchivos.descripcion = this.tituloModalArchivos = '';
                $('#inputArchivo').val('');
            }
            , actualizarEstado (estado, finalTitle, recurso, id)
            {
                var prefijo = estado?'des':'';
                swal({
                title: `Esta seguro de ${prefijo}activar ${finalTitle}?`,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        var uri = `${recurso}/${prefijo}activar/`;
                        this.peticionPatch(uri, id, estado);
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {
                    }
                });
            }
        }
        , mounted() {
            this.listarProcesos();
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
</style>