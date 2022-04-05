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
                    <div class="row justify-content-between">
                        <div class="col-md-auto">
                            <i class="fa fa-align-justify"></i>&nbsp;&nbsp;&nbsp;<span v-text="tituloVista"></span>
                            <button type="button" @click="abrirModal(`registrar${vista?'':'Actividades'}`)" class="btn btn-primary" v-if="permisosUser.crear">
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>                        
                            <button type="button" class="btn btn-secondary" v-else>
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>
                        </div>
                        <div class="col-md-auto" v-show="!vista">
                            <button type="button" @click="abrirModal('planesAccion')" class="btn btn-primary" v-if="permisosUser.crear">
                                <i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Volver
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div v-show="vista">
                        <div class="row justify-content-start">
                            <div class="form-group col-4">
                                <label>Proceso(*)</label>
                                <select class="form-control" v-model="criValFkIdProcesos">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrProceso in criArrProcesos" :key="criArrProceso.id" :value="criArrProceso.id" v-text="criArrProceso.proceso"></option>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Proyecto</label>
                                <select id="criSelProyecto" class="form-control" v-model="criValFkIdProyectos">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrProyecto in criArrProyectos" :key="criArrProyecto.id" :value="criArrProyecto.id" v-text="criArrProyecto.proyecto"></option>
                                </select>
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
                            <div class="form-group col-4">
                                <label>Plan de acción</label>
                                <input type="text" v-model="criValPlanAccion" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <div class="form-group col-4">
                                <label>Responsable</label>
                                <select class="form-control" v-model="criValFkIdResponsUsers">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrUsuario in criArrUsuarios" :key="criArrUsuario.id" :value="criArrUsuario.id" v-text="criArrUsuario.nombre_completo"></option>
                                </select>
                            </div>
                            <div class="form-group col-2">
                                <label>Año</label>
                                <input type="number" min="0" v-model="criValAnio" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="form-group col-2">
                                <button type="submit" @click="listarPlanesAccion(1)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Plan de acción</th>
                                    <th>Año</th>
                                    <th>Puntaje</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="planAccion in arrPlanesAccion" :key="planAccion.id">
                                    <td v-text="planAccion.planAccion"></td>
                                    <td v-text="planAccion.anio"></td>
                                    <td v-text="planAccion.puntaje"></td>
                                    <td v-text="planAccion.nombre_completo"></td>
                                    <td>
                                        <div v-if="planAccion.estado">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Inactivo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" @click="abrirModal('proyectos', {id:planAccion.id, planAccion:planAccion.planAccion, fkIdProcesos:planAccion.fk_id_procesos, fkProcesos:planAccion.proceso})" class="btn btn-info btn-sm" title="Proyectos">
                                            <i class="icon-book-open"></i>
                                        </button>
                                        <button type="button" @click="abrirModal('actualizar', planAccion)" class="btn btn-warning btn-sm" title="Actualizar" v-if="planAccion.estado && permisosUser.actualizar">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="permisosUser.anular">
                                            <button type="button" class="btn btn-danger btn-sm" title="Desactivar" @click="actualizarEstado(planAccion.estado, 'este plan de acción', urlPlanesAccion, planAccion.id, 1)" v-if="planAccion.estado">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" title="Activar" @click="actualizarEstado(planAccion.estado, 'este plan de acción', urlPlanesAccion, planAccion.id, 1)" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash" v-if="planAccion.estado"></i>
                                                <i class="icon-check" v-else></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, Bestado, Bresponsable, Bparticipantes)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, Bestado, Bresponsable, Bparticipantes)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, Bestado, Bresponsable, Bparticipantes)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div v-show="!vista">
                        <div class="row justify-content-between">
                            <div class="form-group col-6">
                                <h6 class="font-italic"><strong>Plan de acción</strong></h6>
                                <label class="text-sm-left font-italic" v-text="planAccion"></label>
                            </div>
                            <div class="form-group col-6">
                                <h6 class="font-italic"><strong>Proceso</strong></h6>
                                <label class="text-sm-left font-italic" v-text="fkProcesos"></label>
                            </div>
                        </div>
                        <div class="row justify-content-start">
                            <div class="form-group col-6">
                                <h6 class="font-italic"><strong>Proyecto</strong></h6>
                                <label class="text-sm-left font-italic" v-text="fkProyectos"></label>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Actividad</th>
                                    <th>Accion</th>
                                    <th>Responsables</th>
                                    <th>Ponderación</th>
                                    <th>Indicador asociado</th>
                                    <th>Dia reporte</th>
                                    <th>Plan accion asociado</th>
                                    <th>Ene</th>
                                    <th>Feb</th>
                                    <th>Mar</th>
                                    <th>Abr</th>
                                    <th>May</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Ago</th>
                                    <th>Sep</th>
                                    <th>Oct</th>
                                    <th>Nov</th>
                                    <th>Dic</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="actividad in arrActividades" :key="actividad.id">
                                    <td class="align-middle" v-text="actividad.actividad"></td>
                                    <td class="align-middle" v-text="actividad.tipoActividad"></td>
                                    <td class="align-middle">
                                        <ul class="list-group">
                                            <li class="list-group-item py-0" v-for="responsable in actividad.responsables" :key="responsable.id" v-text="responsable.nombre_completo"></li>
                                        </ul>
                                    </td>
                                    <td class="align-middle" v-text="actividad.ponderacion"></td>
                                    <td class="align-middle" v-text="actividad.fk_id_indicadores"></td>
                                    <td class="align-middle" v-text="actividad.diaMesLimite"></td>
                                    <td class="align-middle" v-text="actividad.planAccionAsociado"></td>
                                    <td align="center" class="align-middle" v-for="mes in arrMeses">
                                        <button type="button" v-if="actividad[mes.id] != 3" class="circulo border border-dark rounded-circle" :class="arrMesEstado[actividad[mes.id]]"></button>
                                    </td>
                                    <td class="align-middle">
                                        <div v-if="actividad.estado">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Inactivo</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" @click="abrirModal('actualizarActividades', actividad)" class="btn btn-warning btn-sm" title="Actualizar" v-if="actividad.estado && permisosUser.actualizar">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="permisosUser.anular">
                                            <button type="button" class="btn btn-danger btn-sm" title="Desactivar" @click="actualizarEstado(actividad.estado, 'esta actividad', urlActividades, actividad.id, 2)" v-if="actividad.estado">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" title="Activar" @click="actualizarEstado(actividad.estado, 'esta actividad', urlActividades, actividad.id, 2)" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash" v-if="actividad.estado"></i>
                                                <i class="icon-check" v-else></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalRegAct}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModalRegAct"></h4>
                        <button type="button" class="close" @click="cerrarModalRegAct()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Proceso(*)</label>
                                <div class="col-md-8">
                                    <select id="selProcesoRegAct" class="form-control" v-model="fkIdProcesos">
                                        <option value="" disabled>Seleccione</option>
                                        <option v-for="criArrProceso in criArrProcesos" :key="criArrProceso.id" :value="criArrProceso.id" v-text="criArrProceso.proceso"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Proyecto(*)</label>
                                <div class="col-md-8">
                                    <multiselect v-model="valueProyectos" :options="criArrProyectos" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Buscar y seleccionar" label="proyecto" track-by="proyecto" :preselect-first="true">
                                        <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Plan de accion(*)</label>
                                <div class="col-md-8">
                                    <input type="text" v-model="planAccion" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Responsable(*)</label>
                                <div class="col-md-8">
                                    <select class="form-control" v-model="fkResponsUsers">
                                        <option value="" selected>Seleccione</option>
                                        <option v-for="criArrUsuario in criArrUsuarios" :key="criArrUsuario.id" :value="criArrUsuario.id" v-text="criArrUsuario.nombre_completo"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="form-group col-md-2">
                                    <label>Año</label>
                                    <input type="number" min="0" v-model="anio" class="form-control" placeholder="">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Puntaje</label>
                                    <input type="number" min="0" v-model="puntaje" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div v-show="alerta" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in mostrarMsjAlerta" :key="error" v-text="error">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="cerrarModalRegAct()">Cerrar</button>
                        <button type="button" v-text="tipoAccion==1?'Guardar':'Actualizar'" class="btn btn-primary" @click="obtenerDestinosYPlanAccion()"></button>
                        <!--<button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="enviar(2)">Actualizar</button>-->
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Inicio modal lista proyectos-->
        <div v-show="vista">
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalProyectos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div id="modalDialogProyectos" class="modal-dialog modal-primary modal-lg modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalProyectos"></h4>
                            <button type="button" class="close" @click="cerrarModalProyectos()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div id="modalBodyProyectos" class="modal-body">
                            <ul class="list-group list-group-flush">
                                <button type="button" @click="abrirModal('actividades', valueProyecto)" class="list-group-item list-group-item-action list-group-item-info text-center" v-for="valueProyecto in valueProyectos" :key="valueProyecto.id" v-text="valueProyecto.proyecto"></button>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="cerrarModalProyectos()">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="!vista">
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalActividadesRegAct}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalActividadesRegAct"></h4>
                            <button type="button" class="close" @click="cerrarModalActividadesRegAct()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-2">
                                        <label>Tipo actividad</label>
                                        <select class="form-control" v-model="tipoActividad">
                                            <option value="1">Planear</option>
                                            <option value="2">Hacer</option>
                                            <option value="3">Verificar</option>
                                            <option value="4">Actuar</option>
                                            <option value="5">Corregir</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Dia de reporte</label>
                                        <select class="form-control" v-model="diaMesLimite">
                                            <option v-for="dia in 30" :value="dia" v-text="dia"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Responsables</label>
                                        <multiselect v-model="arrResponsRegistrados" :options="criArrUsuarios" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Buscar y seleccionar" label="nombre_completo" track-by="id">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-4">
                                        <label>Meses de ejecución</label>
                                        <multiselect v-model="arrMesesRegistrados" :options="arrMeses" :multiple="true" :close-on-select="false" :clear-on-select="false" :searchable="false" placeholder="Seleccionar" label="mes" track-by="id">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Plan de acción asociado</label>
                                        <select class="form-control" v-model="fkPlanAsocPlanesAccion">
                                            <option v-for="planAccionAsoc in arrPlanesAccionAsoc" :key="planAccionAsoc.id" :value="planAccionAsoc.id" v-text="planAccionAsoc.planAccion"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Actividad</label>
                                        <textarea v-model="actividad" class="form-control" rows="3" placeholder=""></textarea>
                                    </div>
                                </div>
                                <div v-show="alerta" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in mostrarMsjAlerta" :key="error" v-text="error">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" @click="cerrarModalActividadesRegAct()">Cerrar</button>
                            <button type="button" v-text="tipoAccion==1?'Guardar':'Actualizar'" class="btn btn-primary" @click="obtenerDestinosYActividad()"></button>
                            <!--<button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="enviar(tipoAccion)">Actualizar</button>-->
                        </div>
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
        },
        props : ['permisosUser' ,'ruta'],
        data (){
            return {
                urlPlanesAccion : `${this.ruta}/planes_accion`,
                urlActividades : `${this.ruta}/actividades`,

                vista : 1,
                tituloVista : 'Planes de Acción',

                criArrProcesos : [],
                criValFkIdProcesos : '',
                criArrProyectos : [],
                criValFkIdProyectos : '',
                criArrUsuarios : [],
                criValFkIdResponsUsers: '',
                criValPlanAccion : '',
                criValEstado : '',
                criValAnio : 0,
                
                arrPlanesAccion : [],
                id : 0,
                planAccion : '',
                anio : 0,
                puntaje : 0,
                fkIdProcesos : 0,
                fkProcesos : '',
                fkResponsUsers : 0,

                fkIdProyectos : '',
                fkProyectos : '',
                valueProyectos : [],

                alerta : 0,
                mostrarMsjAlerta : [],
                modalRegAct : 0,
                modalProyectos : 0,
                tituloModalRegAct : '',
                tituloModalProyectos : '',
                tipoAccion : 0,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,

                arrActividades : [],
                idActividad : 0,
                actividad : '',
                tipoActividad : '',
                arrResponsRegistrados : [], // PARA UTILIZAR EN EL FORMULARO DE REGISTRO
                arrResponsAntes : [],
                ponderacion : 0,
                fkIdIndicadores : 0, //DESPUES SE CAMBIA A COMILLAS SIMPLES, CUANDO LOS DATOS PARA EL SELECTOR ESTEN DISPONIBLES
                diaMesLimite: '',
                arrPlanesAccionAsoc : [],
                fkPlanAsocPlanesAccion : '', // PARA UTILIZAR EN EL FORMULARO DE REGISTRO
                arrMeses : [{id : 'ene', mes : 'Enero'}, {id : 'feb', mes : 'Febrero'}, {id : 'mar', mes : 'Marzo'}, {id : 'abr', mes : 'Abril'}, {id : 'may', mes : 'Mayo'}, {id : 'jun', mes : 'Junio'}, {id : 'jul', mes : 'Julio'}, {id : 'ago', mes : 'Agosto'}, {id : 'sep', mes : 'Septiembre'}, {id : 'oct', mes : 'Octubre'}, {id : 'nov', mes : 'Noviembre'}, {id : 'dic', mes : 'Diciembre'}],
                arrMesesRegistrados : [],
                arrMesEstado : ['btn btn-warning btn-sm', 'btn btn-success btn-sm', 'btn btn-danger btn-sm'],

                modalActividadesRegAct : 0,
                tituloModalActividadesRegAct : '',

                arrDestinos : [],
                solCorreos : {},
                Reg : 0
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;

            }
        },
        methods : {
            anioActual ()
            {
                var fechaActual = new Date();
                this.criValAnio = fechaActual.getFullYear();
            },
            proyectosRegistrados (idPlanAccion)
            {
                var me = this;
                axios.get(`${this.urlPlanesAccion}/proyectos_registrados/${idPlanAccion}`).then(function (response) {
                    me.valueProyectos = response.data.proyectosRegistrados;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            basicoProcesos ()
            {
                var me = this;
                axios.get(`${this.urlPlanesAccion}/basico_procesos/${this.permisosUser.leer}`).then(function (response) {
                    me.criArrProcesos = response.data.procesos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            basicoProyectos ()
            {
                var me = this;
                axios.get(`${this.urlPlanesAccion}/basico_proyectos/${this.permisosUser.leer}`).then(function (response) {
                    me.criArrProyectos = response.data.proyectos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            basicoUsuarios ()
            {
                var me = this;
                axios.get(`${this.urlPlanesAccion}/basico_usuarios`).then(function (response) {
                    me.criArrUsuarios = response.data.usuarios;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarPlanesAccion (page)
            {
                if (!this.criValFkIdProcesos)return;
                var me = this;
                axios.get(`${this.urlPlanesAccion}?page=${page}&leer=${this.permisosUser.leer}&criValFkIdProcesos=${this.criValFkIdProcesos}&criValFkIdProyectos=${this.criValFkIdProyectos}&criValFkIdResponsUsers=${this.criValFkIdResponsUsers}&criValPlanAccion=${this.criValPlanAccion}&criValEstado=${this.criValEstado}&criValAnio=${this.criValAnio}`).then(function (response) {
                    me.arrPlanesAccion = response.data.planesAccion.data;
                    me.pagination = response.data.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            validarPlanAccion ()
            {
                this.alerta = 0;
                this.mostrarMsjAlerta =[];
                if (!this.planAccion) this.mostrarMsjAlerta.push("El campo plan de accion no puede estar vacío.");
                if (!this.anio) this.mostrarMsjAlerta.push("El campo año no puede estar vacío.");
                if (!this.puntaje) this.mostrarMsjAlerta.push("El campo puntaje no puede estar vacío.");
                if (!this.fkIdProcesos) this.mostrarMsjAlerta.push("Debe seleccionar un proceso.");
                if (!this.fkResponsUsers) this.mostrarMsjAlerta.push("Debe seleccionar un responsable.");

                if (this.mostrarMsjAlerta.length) this.alerta = 1;
                return this.alerta;
            },
            obtenerDestinosYPlanAccion ()
            {
                this.arrResponsRegistrados = [{id : this.fkResponsUsers}];
                if (this.tipoAccion == 1) {
                    this.arrResponsRegistrados[0].tipoCorreo = 1;
                }
                this.solCorreos.proceso = $('#selProcesoRegAct option:selected').text();
                this.organizarYEnviarDestinos();
                if (this.tipoAccion == 1) {
                    this.registrarPlanAccion();
                } else {
                    this.actualizarPlanAccion();
                }
            },
            obtenerDestinosYActividad ()
            {
                if (this.tipoAccion == 1) {
                    this.arrResponsRegistrados.forEach((responRegistrado) => {
                        this.arrDestinos.push({id : responRegistrado.id, tipoCorreo: 1});
                    }, this);
                }
                this.solCorreos.actividad = this.actividad;
                this.solCorreos.proyecto = this.fkProyectos;
                if (this.tipoAccion =! 1) { //VERIFICAR ESTA PARTE
                    this.organizarYEnviarDestinos();
                }
                if (this.tipoCorreo == 1) {
                    this.registrarActividad();
                } else {
                    this.actualizarActividad();
                }
            },
            organizarYEnviarDestinos ()
            {
                // LAS VARIABLES ENVIADAS POR AXIOS NO SE VEN ALTERADAS, SE REUTILIZA A arrResponsRegistrados PARA ENVIAR CORREOS EN LA VISTA
                // PLANES DE ACCION, ESTO SIN CAUSAR INCONVENIENTES PARA LA VISTA ACTIVIDADES.
                if (this.arrResponsAntes.length > 0) {
                    this.arrResponsRegistrados.forEach((responRegistrado) => {
                        var i = true, destino = {id : responRegistrado.id};
                        this.arrResponsAntes.forEach((responAntes) => {
                            if (responRegistrado.id == responAntes.id) {
                                i = false;
                            }
                        }, this);
                        destino.tipoCorreo = i? 1 : 0;
                        this.arrDestinos.push(destino);
                    }, this);
                }
                this.solCorreos.planAccion = this.planAccion;
                this.solCorreos.destinos = this.arrDestinos;
                this.enviarDestinos();
                this.arrDestinos = [];
                this.solCorreos = [];
            },
            enviarDestinos ()
            {
                axios.post(`${this.urlPlanesAccion}/enviar_correo`, this.solCorreos).then(function (response) {}).catch(function (error) {
                    console.log(error);
                });
            },
            registrarPlanAccion ()
            {
                if (this.validarPlanAccion()) return;
                let me = this;
                axios.post(`${this.urlPlanesAccion}/registrar`,{
                    'planAccion': this.planAccion,
                    'anio': this.anio,
                    'puntaje': this.puntaje,
                    'fk_id_procesos': this.fkIdProcesos,
                    'fk_respons_users': this.fkResponsUsers,
                    'valueProyectos' : this.valueProyectos
                }).then(function (response) {
                    me.cerrarModalRegAct();
                    me.listarPlanesAccion(1);
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarPlanAccion ()
            {
               if (this.validarPlanAccion()) return;
                let me = this;
                axios.put(`${this.urlPlanesAccion}/actualizar/${this.id}`,{
                    'planAccion': this.planAccion,
                    'anio': this.anio,
                    'puntaje': this.puntaje,
                    'fkIdProcesos': this.fkIdProcesos,
                    'fkResponsUsers': this.fkResponsUsers,
                    'valueProyectos' : this.valueProyectos
                }).then(function (response) {
                    me.cerrarModalRegAct();
                    me.listarPlanesAccion(1);
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cerrarModalRegAct ()
            {
                this.modalRegAct = 0;
                this.tituloModalRegAct = '';
                this.id = 0;
                this.planAccion = '';
                this.anio = 0;
                this.puntaje = 0;
                this.fkIdProcesos = 0;
                this.fkResponsUsers = 0;
                this.valueProyectos = [];
                this.alerta = 0;
                this.mostrarMsjAlerta = [];
                this.tipoAccion = 0;
            },
            cerrarModalProyectos ()
            {
                this.modalProyectos = 0;
                this.tituloModalProyectos = '';
                this.id = 0;
                this.planAccion = '';
                this.fkIdProcesos = 0;
                this.fkProcesos = '';
                this.valueProyectos = [];
            },
            basicoPlanesAccionAsociables ()
            {
                var me = this;
                axios.get(`${this.urlActividades}/planes_accion_asociables?idProceso=${this.fkIdProcesos}&idPlanAccion=${this.id}`).then(function (response) {
                    me.arrPlanesAccionAsoc = response.data.planesAccionAsoc;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarActividades ()
            {
                var me = this;
                axios.get(`${this.urlActividades}?leer=${this.permisosUser.leer}&fkIdProcesos=${this.fkIdProcesos}&fkIdPlanesAccion=${this.id}&fkIdProyectos=${this.fkIdProyectos}`).then(function (response) {
                    me.arrActividades = response.data.actividades;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            validarActividad ()
            {
                this.alerta = 0;
                this.mostrarMsjAlerta = [];
                if (!this.actividad) this.mostrarMsjAlerta.push("El campo actividad no puede estar vacío.");
                if (!this.tipoActividad) this.mostrarMsjAlerta.push("El campo tipo actividad no puede estar vacío.");
                if (!this.arrResponsRegistrados.length) this.mostrarMsjAlerta.push("Debe seleccionar al menos un responsable.");
                if (!this.arrMesesRegistrados.length) this.mostrarMsjAlerta.push("Debe seleccionar al menos un mes de ejecución.");
                if (!this.diaMesLimite) this.mostrarMsjAlerta.push("Debe seleccionar el día de reporte.");

                if (this.mostrarMsjAlerta.length) this.alerta = 1;
                return this.alerta;
            },
            registrarActividad ()
            {
                if (this.validarActividad()) return;
                let me = this;
                axios.post(`${this.urlActividades}/registrar`,{
                    'actividad': this.actividad
                    , 'tipoActividad': this.tipoActividad
                    , 'responsRegistrados': this.arrResponsRegistrados
                    , 'ponderacion': this.ponderacion
                    , 'fk_id_indicadores': this.fkIdIndicadores
                    , 'diaMesLimite': this.diaMesLimite
                    , 'fk_planAsoc_planes_accion': 0//this.fkPlanAsocPlanesAccion
                    , 'fk_id_procesos': this.fkIdProcesos
                    , 'fk_id_proyectos': this.fkIdProyectos
                    , 'fk_id_planes_accion': this.id
                    , 'arrMesesRegistrados' : this.arrMesesRegistrados
                }).then(function (response) {
                    me.cerrarModalActividadesRegAct();
                    me.listarActividades();

                }).catch(function (error) {
                    console.log(error);
                });
            },
            notificarDestinatariosActividad ()
            {
                return 0;
            },
            actualizarActividad ()
            {
                if (this.validarActividad()) return;
                let me = this;
                axios.put(`${this.urlActividades}/actualizar/${this.idActividad}`,{
                    'actividad': this.actividad
                    , 'tipoActividad': this.tipoActividad
                    , 'responsRegistrados': this.arrResponsRegistrados
                    , 'ponderacion': this.ponderacion
                    , 'fkIdIndicadores': this.fkIdIndicadores
                    , 'diaMesLimite': this.diaMesLimite
                    , 'fkPlanAsocPlanesAccion': this.fkPlanAsocPlanesAccion
                    , 'arrMesesRegistrados' : this.arrMesesRegistrados
                }).then(function (response) {
                    me.cerrarModalActividadesRegAct();
                    me.listarActividades();
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cerrarModalActividadesRegAct ()
            {
                this.modalActividadesRegAct = 0;
                this.tituloModalActividadesRegAct = '';
                this.idActividad = 0;
                this.actividad = '';
                this.tipoActividad = '';
                this.diaMesLimite = '';
                this.arrResponsRegistrados = [];
                this.arrMesesRegistrados = [];
                this.fkPlanAsocPlanesAccion = '';
            },
            actualizarEstado (estado, finalTitle, url, id, listar)
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
                                case 1:me.listarPlanesAccion(1);break;
                                case 2:me.listarActividades();break;
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
            },
            abrirModal(accion, data = [])
            {
                switch (accion) {
                    case 'registrar':
                        this.tituloModalRegAct = 'Registrar plan de acción';
                        this.fkIdProcesos = this.criValFkIdProcesos;
                        this.valueProyectos = this.criValFkIdProyectos ? [{'id':this.criValFkIdProyectos , 'proyecto':$('#criSelProyecto option:selected').text()}]:[];
                        this.planAccion = this.criValPlanAccion;
                        this.fkResponsUsers = this.criValFkIdResponsUsers;
                        this.anio = this.criValAnio;
                        this.modalRegAct = this.tipoAccion = 1;
                        break;
                    case 'actualizar':
                        this.tituloModalRegAct = 'Actualizar plan de acción';
                        this.id = data.id;
                        this.planAccion = data.planAccion;
                        this.anio = data.anio;
                        this.puntaje = data.puntaje;
                        this.fkIdProcesos = data.fk_id_procesos;
                        this.fkResponsUsers = data.fk_respons_users;
                        this.arrResponsAntes = [{id : data.fk_respons_users}]; // PARA RECORDAR LOS RESPONSABLES ANTES DE ALGUNA MODIFICACION DE ESTOS.
                        this.proyectosRegistrados(data.id);
                        this.tipoAccion = 2;
                        this.modalRegAct = 1;
                        break;
                    case 'proyectos':
                        this.tituloModalProyectos = `Proyectos de ${data.planAccion}`;
                        this.id = data.id;
                        this.planAccion = data.planAccion;
                        this.fkIdProcesos = data.fkIdProcesos;
                        this.fkProcesos = data.fkProcesos;
                        this.proyectosRegistrados(data.id);
                        this.modalProyectos = 1;
                        break;
                    case 'actividades':
                        this.tituloVista = 'Actividades';
                        this.fkIdProyectos = data.id;
                        this.fkProyectos = data.proyecto;
                        this.listarActividades();
                        this.basicoPlanesAccionAsociables();
                        this.vista = 0;
                        break;
                    case 'planesAccion':
                        this.tituloVista = 'Planes de acción';
                        this.fkIdProyectos = '';
                        this.fkProyectos = '';
                        this.arrActividades = [];
                        this.arrPlanesAccionAsoc = [];
                        this.vista = 1;
                        break;
                    case 'registrarActividades':
                        this.tituloModalActividadesRegAct = 'Registrar actividad';
                        this.tipoAccion = 1;
                        this.modalActividadesRegAct = 1;
                        break;
                    case 'actualizarActividades':
                        this.tituloModalActividadesRegAct = 'Actualizar actividad';
                        this.idActividad = data.id;
                        this.actividad = data.actividad;
                        var tipoActividad = 0;
                        switch (data.tipoActividad) {
                            case 'Planear':tipoActividad = 1;break;
                            case 'Hacer':tipoActividad = 2;break;
                            case 'Verificar':tipoActividad = 3;break;
                            case 'Actuar':tipoActividad = 4;break;
                            case 'Corregir':tipoActividad = 5;break
                        }
                        this.tipoActividad = tipoActividad;
                        this.arrResponsRegistrados = data.responsables;
                        this.arrResponsAntes = data.responsables; // PARA RECORDAR LOS RESPONSABLES ANTES DE ALGUNA MODIFICACION DE ESTOS.
                        this.ponderacion = data.ponderacion;
                        this.fkIdIndicadores = data.fkIdIndicadores;
                        this.diaMesLimite = data.diaMesLimite;
                        this.fkPlanAsocPlanesAccion = data.id_plan_asoc;
                        this.arrMeses.forEach( (mes) => {
                            if (data[mes.id] != 3) this.arrMesesRegistrados.push(mes);
                        }, this);
                        this.tipoAccion = 2;
                        this.modalActividadesRegAct = 1;
                        break;
                }
            }
        },
        mounted() {
            this.basicoProcesos();
            this.basicoProyectos();
            this.basicoUsuarios();
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
    #modalDialogProyectos{
    overflow-y: initial !important
    }
    #modalBodyProyectos{
    height: 400px;
    overflow-y: auto;
    }
    .circulo{
        width: 1.5em;
        height: 1.5em;
    }
</style>