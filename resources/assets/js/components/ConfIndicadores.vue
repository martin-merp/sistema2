<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col-md-auto">
                            <i class="fa fa-align-justify"></i>&nbsp;&nbsp;&nbsp;<span v-text="tituloVista"></span>
                            <button type="button" @click="abrirModal(`registrar${vista == 0? '' : 'Indicadores'}`)" class="btn btn-primary" v-if="permisosUser.crear" v-show="vista != 2">
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>                        
                            <button type="button" class="btn btn-secondary" v-else v-show="vista != 2">
                                <i class="icon-plus"></i>&nbsp;Nuevo    
                            </button>
                        </div>
                        <div class="col-md-auto" v-show="vista != 0">
                            <button type="button" @click="abrirModal(vista == 1? 'objetivos' : 'volverIndicadores')" class="btn btn-primary">
                                <i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Volver
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div v-show="vista == 0">
                        <div class="row justify-content-start">
                            <div class="form-group col-4">
                                <label>Objetivo</label>
                                <input type="text" v-model="criValObjetivo" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <div class="form-group col-4">
                                <label>Descripcion</label>
                                <input type="text" v-model="criValDescripcion" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <div class="form-group col-2">
                                <label>Estado</label>
                                <select class="form-control" v-model="criValEstado">
                                    <option value="" selected>Seleccione</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="form-group col-2">
                                <button type="submit" @click="listarObjetivos(1)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Objetivo</th>
                                    <th>Descripcion</th>
                                    <th>Responsable</th>
                                    <th>Indicadores</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(objetivo, index) in arrObjetivos" :key="objetivo.id">
                                    <td v-text="index + 1"></td>
                                    <td v-text="objetivo.objetivo"></td>
                                    <td v-text="objetivo.descripcion"></td>
                                    <td v-text="objetivo.nombre_completo"></td>
                                    <td>
                                        <button type="button" class="btn btn-light border border-secondary btn-sm" @click="abrirModal('indicadores', {id:objetivo.id, objetivo:objetivo.objetivo})" v-if="objetivo.estado">
                                            <i class="fa fa-line-chart" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                            <i class="fa fa-line-chart" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div v-if="objetivo.estado">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Inactivo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" @click="abrirModal('actualizar', objetivo)" class="btn btn-warning btn-sm" title="Actualizar" v-if="objetivo.estado && permisosUser.actualizar">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="permisosUser.anular">
                                            <button type="button" class="btn btn-danger btn-sm" title="Desactivar" @click="actualizarEstado(objetivo.estado, 'este objetivo', urlObjetivos, objetivo.id, 1)" v-if="objetivo.estado">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" title="Activar" @click="actualizarEstado(objetivo.estado, 'este objetivo', urlObjetivos, objetivo.id, 1)" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash" v-if="objetivo.estado"></i>
                                                <i class="icon-check" v-else></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-show="vista == 1">
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">Estado</label>
                            <div class="col-sm-2">
                                <select class="form-control" v-model="criValEstadoIndicador">
                                    <option value="" selected>Seleccione</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" @click="listarIndicadores()" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Indicador</th>
                                    <th>Descripcion</th>
                                    <th>Meta</th>
                                    <th>Acumulativo</th>
                                    <th>Actividades</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="indicador in arrIndicadores" :key="indicador.id">
                                    <td v-text="indicador.indicador"></td>
                                    <td v-text="indicador.descripcion"></td>
                                    <td v-text="indicador.meta"></td>
                                    <td>
                                        <div v-if="indicador.acumulativo">
                                            <h6><span class="badge badge-pill badge-light">Si</span></h6>
                                        </div>
                                        <div v-else>
                                            <h6><span class="badge badge-pill badge-dark">No</span></h6>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-light border border-secondary btn-sm" @click="abrirModal('actividades', {idIndicador : indicador.id, indicador : indicador.indicador})" v-if="indicador.estado">
                                            <i class="fa fa-tasks" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                            <i class="fa fa-tasks" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div v-if="indicador.estado">
                                            <span class="badge badge-pill badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-pill badge-danger">Inactivo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" @click="abrirModal('actualizarIndicadores', indicador)" class="btn btn-warning btn-sm" title="Actualizar" v-if="indicador.estado && permisosUser.actualizar">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="permisosUser.anular">
                                            <button type="button" class="btn btn-danger btn-sm" title="Desactivar" @click="actualizarEstado(indicador.estado, 'este indicador', urlIndicadores, indicador.id, 2)" v-if="indicador.estado">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" title="Activar" @click="actualizarEstado(indicador.estado, 'este indicador', urlIndicadores, indicador.id, 2)" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash" v-if="indicador.estado"></i>
                                                <i class="icon-check" v-else></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-show="vista == 2">
                        <div class="row justify-content-start">
                            <div class="form-group col-4">
                                <label>Proceso(*)</label>
                                <select class="form-control" v-model="criValFkIdProcesos">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrProceso in criArrProcesos" :key="criArrProceso.id" :value="criArrProceso.id" v-text="criArrProceso.proceso"></option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Plan Acción(*)</label>
                                <select class="form-control" v-model="criValFkIdPlanesAccion">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrPlanAccion in criArrPlanesAccion[criValFkIdProcesos]" :key="criArrPlanAccion.id" :value="criArrPlanAccion.id" v-text="criArrPlanAccion.planAccion"></option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Proyecto</label>
                                <select id="criSelProyecto" class="form-control" v-model="criValFkIdProyectos">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrProyecto in criArrProyectos" :key="criArrProyecto.id" :value="criArrProyecto.id" v-text="criArrProyecto.proyecto"></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-sm-1 col-form-label">Ordenar</label>
                            <div class="col-2">
                                <select class="form-control" v-model="criValColumnaOrden">
                                    <option value="" selected>Seleccione</option>
                                    <option value="actividad">ACTIVIDAD</option>
                                    <option value="proyecto">PROYECTO</option>
                                    <option value="ponderacion">PONDERACIÓN</option>
                                </select>
                            </div>
                            <label class="col-sm-1 col-form-label">Dirección</label>
                            <div class="col-2">
                                <select class="form-control" v-model="criValDireccion">
                                    <option value="asc">ASCENDENTE</option>
                                    <option value="desc">DESCENDENTE</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <button type="submit" @click="listarActividades()" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <div class="row justify-content-around">
                            <div class="col-md-auto">
                                <table class="table table-bordered table-striped table-sm">
                                    <caption class="caption-on-top">ASIGNADAS</caption>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Actividad</th>
                                            <th>Proyecto</th>
                                            <th>Ponderación</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(actividad, index) in arrActividadesAtadas">
                                            <td v-text="index + 1"></td>
                                            <td v-text="actividad.actividad"></td>
                                            <td v-text="actividad.proyecto"></td>
                                            <td v-text="actividad.ponderacion"></td>
                                            <td><a class="btn btn-danger" @click="desatarActividad(actividad.id, index)"><i class="fa fa-minus fa-lg" aria-hidden="true"></i></a></td> <!-- INCLUIR METODO PARA TRASLADAR -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-auto">
                                <table class="table table-bordered table-striped table-sm">
                                    <caption class="caption-on-top">SIN ASIGNAR</caption>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>Actividad</th>
                                            <th>Proyecto</th>
                                            <th>Ponderación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(actividad, index) in arrActividadesDesatadas">
                                            <td><a class="btn btn-primary" @click="atarActividad(actividad.id, index)"><i class="fa fa-plus fa-lg" aria-hidden="true"></i></a></td> <!-- INCLUIR METODO PARA TRASLADAR -->
                                            <td v-text="index + 1"></td>
                                            <td v-text="actividad.actividad"></td>
                                            <td v-text="actividad.proyecto"></td>
                                            <td v-text="actividad.ponderacion"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                            <div class="row justify-content-center">
                                <div class="form-group col-md-8">
                                    <label>Objetivo</label>
                                    <input type="text" v-model="objetivo" class="form-control" placeholder="Nombre del objetivo">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Clasificación</label>
                                    <select v-model="clasificacion" class="form-control" style="width:106%">
                                        <option value="" selected>Seleccione</option>
                                        <option value="1">HSE</option>
                                        <option value="2">CALIDAD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="form-group col-md-8">
                                    <label>Responsable</label>
                                    <multiselect v-model="responsableElegido" :options="arrResponsables" :close-on-select="false" :clear-on-select="false" :searchable="true" placeholder="Seleccionar" label="nombre_completo" track-by="fkResponsUsers"></multiselect>
                                </div>
                                <div class="form-group col-md-2">
                                <label>Año</label>
                                <input type="number" min="0" v-model="anio" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-6">
                                    <label>Descripción</label>
                                    <textarea v-model="descripcion" class="form-control" rows="3" placeholder=""></textarea>
                                </div>
                            </div>
                            <div v-show="alerta" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in mostrarMsjAlerta" :key="error" v-text="error"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarObjetivo()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarObjetivo()">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalIndicadoresRegAct}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModalIndicadoresRegAct"></h4>
                        <button type="button" class="close" @click="cerrarModalIndicadoresRegAct()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-3">
                                    <label>Meta</label>
                                    <input type="text" min="0" max="100" v-model="meta" class="form-control" aria-describedby="addon-percent">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="addon-percent">%</span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" v-model="acumulativo" id="checkAcumulador">
                                        <label for="checkAcumulador">Es acumulativo</label>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Definición</label>
                                    <select v-model="definicion" class="form-control">
                                        <option value="" selected>Seleccione</option>
                                        <option value="1">ESTRUCTURA</option>
                                        <option value="2">PROCESO</option>
                                        <option value="3">RESULTADO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-9">
                                    <label>Indicador</label>
                                    <input type="text" v-model="indicador" class="form-control" placeholder="Nombre del indicador">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="form-group col-md-5">
                                    <label>Descripción</label>
                                    <textarea v-model="descripcionIndicador" class="form-control" rows="4"></textarea>
                                </div>
                                <div class="form-group col-md-5">
                                    <label>Observación</label>
                                    <textarea v-model="observaciones" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div v-show="alerta" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in mostrarMsjAlerta" :key="error" v-text="error"></div>
                                </div>
                            </div>
                        </form>                                            
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModalIndicadoresRegAct()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarIndicador()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarIndicador()">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    import Multiselect from 'vue-multiselect'
    export default {
        components: {
            Multiselect
        }
        , props : ['permisosUser' ,'ruta']
        , data ()
        {
            return {
                urlObjetivos : `${this.ruta}/objetivos`
                , urlIndicadores : `${this.ruta}/indicadores`
                , urlIndicadoresActividades : `${this.ruta}/indicadores_actividades`
                
                , vista : 0
                , tituloVista : 'Objetivos'
                
                , criValObjetivo : ''
                , criValDescripcion : ''
                , criValEstado : ''


                , arrObjetivos : []

                , criValEstadoIndicador : ''
                , arrIndicadores : []

                , modal : 0
                , tituloModal : ''

                , objetivo : ''
                , responsableElegido : {}
                , arrResponsables : []
                , clasificacion : ''
                , anio : 0
                , descripcion : ''
                , id : 0

                , alerta : 0
                , mostrarMsjAlerta : []
                , tipoAccion : 0

                , modalIndicadoresRegAct : 0
                , tituloModalIndicadoresRegAct : ''
                
                , meta : 0
                , acumulativo : 0
                , definicion : ''
                , indicador : ''
                , descripcionIndicador : ''
                , observaciones : ''
                , idIndicador : 0

                , criArrProcesos : []
                , criValFkIdProcesos : 0
                , criArrPlanesAccion : []
                , criValFkIdPlanesAccion : 0
                , criArrProyectos : []
                , criValFkIdProyectos : 0
                , criValColumnaOrden : ''
                , criValDireccion : 'asc'

                , arrActividadesAtadas : []
                , arrActividadesDesatadas : []
            }
        }
        , methods : {
            anioActual ()
            {
                var fechaActual = new Date();
                this.anio = fechaActual.getFullYear();
            }
            , basicoProcesos ()
            {
                var me = this;
                axios.get(`${this.ruta}/planes_accion/basico_procesos/${this.permisosUser.leer}`).then(function (response) {
                    me.criArrProcesos = response.data.procesos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , basicoPlanesAccion ()
            {
                var me = this;
                axios.get(`${this.urlIndicadoresActividades}/basico_planes_accion/${this.permisosUser.leer}`).then(function (response) {
                    me.criArrPlanesAccion = response.data.planesAccion;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , basicoProyectos ()
            {
                var me = this;
                axios.get(`${this.ruta}/planes_accion/basico_proyectos/${this.permisosUser.leer}`).then(function (response) {
                    me.criArrProyectos = response.data.proyectos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , basicoUsuarios ()
            {
                var me = this;
                axios.get(`${this.urlObjetivos}/basico_usuarios`).then(function (response) {
                    me.arrResponsables = response.data.usuarios;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , listarObjetivos ()
            {
                let me = this;
                axios.get(`${this.urlObjetivos}?leer=${this.permisosUser.leer}&criValObjetivo=${this.criValObjetivo}&criValDescripcion=${this.criValDescripcion}&criValEstado=${this.criValEstado}`).then(function (response) {
                    me.arrObjetivos = response.data.objetivos;
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , validarObjetivo ()
            {
                this.alerta = 0;
                this.mostrarMsjAlerta =[];
                if (!this.objetivo) this.mostrarMsjAlerta.push("El campo objetivo no puede estar vacío.");
                if (jQuery.isEmptyObject(this.responsableElegido)) this.mostrarMsjAlerta.push("Debe seleccionar un responsable.");
                if (!this.clasificacion) this.mostrarMsjAlerta.push("El campo clasificación no puede estar vacío.");
                if (!this.anio) this.mostrarMsjAlerta.push("El campo año no puede estar vacío.");
                if (!this.descripcion) this.mostrarMsjAlerta.push("El campo descripción no puede estar vacío.");
                if (this.mostrarMsjAlerta.length) this.alerta = 1;
                return this.alerta;
            }
            , registrarObjetivo ()
            {
                if (this.validarObjetivo()) return;
                let me = this;
                axios.post(`${this.urlObjetivos}/registrar`, {
                    objetivo : this.objetivo
                    , fk_respons_users : this.responsableElegido.fkResponsUsers
                    , clasificacion : this.clasificacion
                    , anio : this.anio
                    , descripcion : this.descripcion
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarObjetivos();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , actualizarObjetivo ()
            {
                if (this.validarObjetivo()) return;
                let me = this;
                axios.put(`${this.urlObjetivos}/actualizar/${this.id}`,{
                    objetivo : this.objetivo
                    , fkResponsUsers : this.responsableElegido.fkResponsUsers
                    , clasificacion : this.clasificacion
                    , anio : this.anio
                    , descripcion : this.descripcion
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarObjetivos();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , cerrarModal ()
            {
                this.modal = 0;
                this.tituloModal = '';
                this.id = 0;
                this.objetivo = '';
                this.responsableElegido = {};
                this.clasificacion = '';
                this.anioActual();
                this.descripcion = '';
                this.alerta = 0;
                this.mostrarMsjAlerta = [];
                this.tipoAccion = 0;
            }
            , listarIndicadores ()
            {
                let me = this;
                axios.get(`${this.urlIndicadores}?leer=${this.permisosUser.leer}&fkIdObjetivos=${this.id}&criValEstadoIndicador=${this.criValEstadoIndicador}`).then(function (response) {
                    me.arrIndicadores = response.data.indicadores;
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , validarIndicador ()
            {
                this.alerta = 0;
                this.mostrarMsjAlerta =[];
                if (!this.meta) this.mostrarMsjAlerta.push("El campo meta no puede estar vacío.");
                if (!this.definicion) this.mostrarMsjAlerta.push("Debe seleccionar una definición.");
                if (!this.indicador) this.mostrarMsjAlerta.push("El campo indicador no puede estar vacío.");
                if (!this.descripcionIndicador) this.mostrarMsjAlerta.push("El campo descripción no puede estar vacío.");
                if (this.mostrarMsjAlerta.length) this.alerta = 1;
                return this.alerta;
            }
            , registrarIndicador ()
            {
                if (this.validarIndicador()) return;
                let me = this;
                axios.post(`${this.urlIndicadores}/registrar`, {
                    meta : this.meta
                    , acumulativo : this.acumulativo
                    , definicion : this.definicion
                    , indicador : this.indicador
                    , descripcion : this.descripcionIndicador
                    , observaciones : this.observaciones
                    , fk_id_objetivos : this.id
                }).then(function (response) {
                    me.cerrarModalIndicadoresRegAct();
                    me.listarIndicadores();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , actualizarIndicador ()
            {
                if (this.validarIndicador()) return;
                let me = this;
                axios.put(`${this.urlIndicadores}/actualizar/${this.idIndicador}`,{
                    meta : this.meta
                    , acumulativo : this.acumulativo
                    , definicion : this.definicion
                    , indicador : this.indicador
                    , descripcion : this.descripcionIndicador
                    , observaciones : this.observaciones
                }).then(function (response) {
                    me.cerrarModalIndicadoresRegAct();
                    me.listarIndicadores();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , cerrarModalIndicadoresRegAct ()
            {
                this.modalIndicadoresRegAct = 0;
                this.tituloModalIndicadoresRegAct = '';
                this.idIndicador = 0;
                this.meta = 0;
                this.acumulativo = 0;
                this.definicion = '';
                this.indicador = '';
                this.descripcionIndicador = '';
                this.observaciones = '';
                this.alerta = 0;
                this.mostrarMsjAlerta = [];
                this.tipoAccion = 0;
            }
            , actualizarEstado (estado, finalTitle, url, id, listar)
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
                        let me = this;
                        axios.patch(`${url}/${prefijo}activar/${id}`,{
                        }).then(function (response) {
                            switch (listar) {
                                case 1:me.listarObjetivos();break;
                                case 2:me.listarIndicadores();break;
                            }
                            swal(
                            `${estado?'Desa':'A'}ctivado!`,
                            `El registro ha sido ${prefijo}activado con éxito.`,
                            'success'
                            )
                        }).catch(function (error) {
                            console.log(error);
                        });
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === swal.DismissReason.cancel
                    ) {
                        
                    }
                });
            }
            , listarActividades ()
            {
                let me = this;
                axios.get(`${this.urlIndicadoresActividades}?leer=${this.permisosUser.leer}&criValFkIdPlanesAccion=${this.criValFkIdPlanesAccion}&criValFkIdProyectos=${this.criValFkIdProyectos}&criValColumnaOrden=${this.criValColumnaOrden}&criValDireccion=${this.criValDireccion}`).then(function (response) {
                    me.arrActividadesAtadas = response.data.actividades.atadas;
                    me.arrActividadesDesatadas = response.data.actividades.desatadas;
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , desatarActividad (idActividad, indice)
            {
                axios.post(`${this.urlIndicadoresActividades}/desatar`, {idIndicador : this.idIndicador, fkIdActividades: idActividad}).then(function (response) {})
                .catch(function (error) {
                    console.log(error);
                });
                this.trasladarActividad(indice, this.arrActividadesAtadas, this.arrActividadesDesatadas);
            }
            , atarActividad (idActividad, indice)
            {
                axios.post(`${this.urlIndicadoresActividades}/atar`,{idIndicador : this.idIndicador, fkIdActividades: idActividad}).then(function (response) {})
                .catch(function (error) {
                    console.log(error);
                });
                this.trasladarActividad(indice, this.arrActividadesDesatadas, this.arrActividadesAtadas);
            }
            , trasladarActividad (indice, arrSalida, arrEntrada)
            {
                var actividad = arrSalida[indice], ultimo = arrEntrada.length - 1;
                arrSalida.splice(indice, 1);
                if (ultimo > -1) {
                    for (var i = 0; i < arrEntrada.length; i++) {
                        if (actividad.indice < arrEntrada[i].indice) {
                            arrEntrada.splice(i, 0, actividad);
                            break;
                        } else if (i == ultimo) {
                            arrEntrada.push(actividad);
                        }
                    }
                } else {
                    arrEntrada.push(actividad);
                }
            }
            , abrirModal (accion, data = {})
            {
                switch (accion) {
                    case 'registrar':
                        this.tituloModal = 'Registrar objetivo';
                        this.tipoAccion = 1;
                        this.modal = 1;
                        break;
                    case 'actualizar':
                        this.tituloModal = 'Actualizar objetivo';
                        this.tipoAccion = 2;
                        this.objetivo = data.objetivo;
                        this.responsableElegido = {fkResponsUsers : data.fk_respons_users, nombre_completo : data.nombre_completo};
                        this.clasificacion = data.clasificacion == 'HSE' ? 1:2;
                        this.anio = data.anio;
                        this.descripcion = data.descripcion;
                        this.id = data.id;
                        this.modal = 1;
                        break;
                    case 'indicadores':
                        this.id = data.id;
                        this.tituloVista = 'Indicadores';
                        this.listarIndicadores();
                        this.vista = 1;
                        break;
                    case 'objetivos':
                        this.vista = 0;
                        this.tituloVista = 'Objetivos';                                            
                        this.arrIndicadores = [];
                        this.id = 0;
                        break;
                    case 'registrarIndicadores':
                        this.tituloModalIndicadoresRegAct = 'Registrar indicador';
                        this.tipoAccion = 1;
                        this.modalIndicadoresRegAct = 1;
                        break;
                    case 'actualizarIndicadores':
                        this.tituloModalIndicadoresRegAct = 'Actualizar indicador';
                        this.meta = data.meta;
                        this.acumulativo = data.acumulativo;
                        this.definicion = data.definicion == 'ESTRUCTURA' ? 1 : data.definicion == 'PROCESO' ? 2 : 3;
                        this.indicador = data.indicador;
                        this.descripcionIndicador = data.descripcion;
                        this.observaciones = data.observaciones;
                        this.idIndicador = data.id;
                        this.tipoAccion = 2;
                        this.modalIndicadoresRegAct = 1;
                        break;
                    case 'actividades':
                        this.idIndicador = data.idIndicador;
                        this.tituloVista = `Gestión actividades - ${data.indicador}`;
                        this.vista = 2;
                        break;
                    case 'volverIndicadores':
                        this.vista = 1;
                        this.idIndicador = 0;
                        this.arrActividadesAtadas = [];
                        this.arrActividadesDesatadas = [];
                        this.tituloVista = 'Indicadores';
                        break;
                }
            }
        }
        , mounted ()
        {
            this.listarObjetivos();
            this.basicoUsuarios();
            this.basicoPlanesAccion();
            this.basicoProcesos();
            this.basicoProyectos();
            this.anioActual();
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
    .caption-on-top{
        caption-side: top;
        text-align: center;
    }
</style>