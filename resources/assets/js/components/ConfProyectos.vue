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
                        <i class="fa fa-align-justify"></i> Proyectos
                        <button type="button" @click="abrirModal('proyectos','registrar')" class="btn btn-primary" v-if="permisosUser.crear">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button type="button" class="btn btn-secondary" v-else>
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                            <label>Proyecto</label>
                                <div class="input-group">
                                    <!--<input type="hidden" v-model="criterio" value="proyecto" >-->
                                    <input type="text" v-model="buscar" @keyup.enter="listarProyecto(1,buscar,Bestado,Bresponsable,Bparticipantes)" class="form-control" placeholder="Texto a buscar">
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label>Estado</label>
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="Bestado" @keyup.enter="listarProyecto(1,buscar,Bestado,Bresponsable,Bparticipantes)">
                                      <option value="" selected>Seleccione</option>
                                      <option value="1">Activo</option>
                                      <option value="0">Inactivo</option>
                                      <option value="2">Finalizado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                            <label>Responsable</label>
                                <div class="input-group">
                                    <!--<input type="hidden" v-model="criterio" value="proyecto" >-->
                                    <input type="text" v-model="Bresponsable" @keyup.enter="listarProyecto(1,buscar,Bestado,Bresponsable,Bparticipantes)" class="form-control" placeholder="Texto a buscar">
                                </div>
                            </div>
                            <div class="col-md-6">
                            <label>Participantes</label>
                                <div class="input-group">
                                    <select class="form-control col-md-6" v-model="Bparticipantes" @keyup.enter="listarProyecto(1,buscar,Bestado,Bresponsable,Bparticipantes)">
                                        <option value="" selected>Seleccione</option>
                                        <option v-for="asociado in arrAsocBusqueda" :key="asociado.id" :value="asociado.id" v-text="asociado.nombre_completo"></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button  type="submit" @click="listarProyecto(1,buscar,Bestado,Bresponsable,Bparticipantes)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Proyecto</th>
                                    <th>Descripcion</th>
                                    <th>Responsable</th>
                                    <th>Correo ProductOwner</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proyecto in arrayProyecto" :key="proyecto.id">
                                    <td v-text="proyecto.proyecto"></td>
                                    <td v-text="proyecto.descripcion"></td>
                                    <td :value="proyecto.fk_respons_users" v-text="proyecto.nombre_completo"></td>
                                    <td v-text="proyecto.correoProductOwner"></td>
                                    <td>
                                        <div v-if="proyecto.estado==0">
                                            <span class="badge badge-danger">Inactivo</span>
                                        </div>
                                        <div v-if="proyecto.estado==1">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-if="proyecto.estado==2">
                                            <span class="badge badge-info">Finalizado</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" @click="abrirModal('proyectos','participantes', proyecto)" class="btn btn-primary btn-sm" title="Personal y procesos">
                                          <i class="icon-list"></i>
                                        </button>
                                        <button type="button" @click="abrirModal('proyectos','actualizar',proyecto)" class="btn btn-warning btn-sm" title="Actualizar" v-if="(proyecto.estado == 1) && permisosUser.actualizar">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <button v-else type="button" class="btn btn-secondary btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="proyecto.estado == 1">
                                            <button type="button" class="btn btn-danger btn-sm" title="Desactivar" @click="desactivarProyecto(proyecto.id)" v-if="permisosUser.anular">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" title="Finalizar" @click="FinalizarProyecto(proyecto.id)">
                                                <i class="fa fa-square-o" aria-hidden="true"></i>
                                            </button>
                                        </template>
                                        <template v-else-if="proyecto.estado == 0">
                                            <button type="button" class="btn btn-success btn-sm" title="Activar" @click="activarProyecto(proyecto.id)" v-if="permisosUser.anular">
                                                <i class="icon-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-square-o" aria-hidden="true"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" title="Reactivar" @click="activarProyecto(proyecto.id)" v-if="permisosUser.actualizar">
                                                <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                <i class="fa fa-check-square-o" aria-hidden="true"></i>
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
                            <div class="row">
                                <div class="form-group col-md-12 s">
                                    <label class="col-md-2 form-control-label "  for="text-input">Proyecto (*)</label>
                                    <div class="col-md-10 float-right">
                                        <input type="text" v-model="proyecto" class="form-control" placeholder="Nombre del proyecto">                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12 s">
                                    <label class="col-md-2 form-control-label" for="text-input">Descripción</label>
                                    <div class="col-md-10 float-right">
                                        <input type="text" v-model="descripcion" class="form-control" placeholder="Descripción del proyecto">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 s">
                                    <label class="col-md-4 form-control-label float-left" for="email-input">Responsable</label>
                                    <div class="col-md-8 float-right">
                                        <select v-model="responsable" class="form-control">
                                            <option value="0" disabled>Seleccione</option>
                                            <option v-for="respon in arrayRespon" :key="respon.id" :value="respon.id" v-text="respon.nombre_completo"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 s">
                                    <label class="col-md-4 form-control-label float-left" for="email-input">Participantes</label>
                                    <div class="col-md-8 float-right">
                                        <!--<label class="typo__label">Participantes</label>-->        
                                        <multiselect v-model="value" :options="arrayRespon" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Buscar y seleccionar" label="nombre_completo" track-by="id">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                        </multiselect>
                                        <!--<pre class="language-json"><code>{{ value  }}</code></pre>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 form-control-label" for="text-input">Correo Productouner</label>
                                    <div class="col-md-8 float-right">
                                        <input type="email" v-model="correo_productouner" class="form-control" placeholder="">                                        
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="col-md-4 form-control-label" for="text-input">Procesos</label>
                                    <div class="col-md-8 float-right">
                                        <!-- AQUI VA EL MULTISELECT DE PROCESOS -->
                                        <multiselect v-model="valueProcesos" :options="arrProcesos" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Seleccionar" label="proceso" track-by="proceso" :searchable="false" :preselect-first="true">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                        </multiselect>
                                    </div>
                                </div>
                            </div>
                                <div v-show="errorProyecto" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjProyecto" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarProyecto()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarProyecto()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
             <!--Inicio modal participantes-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalParticipantes}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModalParticipantes()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm">
                                        <label><strong>Personal asociado</strong></label>
                                        <ul class="list-group">
                                            <li class="list-group-item" v-for="asociados in arrayPartici" :key="asociados.id" v-text="asociados.nombre_completo"></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm">
                                        <label><strong>Procesos</strong></label>
                                        <ul class="list-group">
                                            <li class="list-group-item" v-for="procesos in valueProcesos" :key="procesos.id" v-text="procesos.proceso"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModalParticipantes()">Cerrar</button>
                            <!-- DEBE MODIFICARSE------------------------------------------------------------------------------------------>
                            
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin modal participantes-->
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
                value: [],
                options: [],
                valueProcesos : [],
                arrProcesos : [],
                proyecto_id: 0,
                proyecto : '',
                descripcion : '',
                responsable: 0,
                correo_productouner: '',
                id: '',
                arrayProyecto : [],
                arrayRespon: [],
                arrayPartici: [],
                arrAsocBusqueda: [],
                modal : 0,
                modalParticipantes : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorProyecto : 0,
                errorMostrarMsjProyecto : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'proyecto',
                buscar : '',
                Bestado: '',
                Bresponsable: '',
                Bparticipantes: ''
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
            abrirModal(modelo, accion, data = []){
                this.sResponsables();
                this.sProcesos();
                switch(modelo){
                    case "proyectos":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Proyecto ';
                                this.proyecto= '';
                                this.descripcion='';
                                this.responsable='';
                                this.correo_productouner='';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar Proyecto';
                                this.tipoAccion=2;
                                this.proyecto_id=data['id'];
                                this.proyecto = data['proyecto'];
                                this.descripcion = data['descripcion'];
                                this.responsable = data['fk_respons_users'];
                                this.correo_productouner = data['correoProductOwner'];
                                this.multiSelectAsociados(data['id']);
                                break;
                            }
                            case 'participantes':
                            {
                                this.modalParticipantes=1;
                                this.tituloModal = data['proyecto'];
                                this.proyecto_id=data['id'];
                                this.asociados(data['id']);
                                this.multiSelectProcesos(data.id);
                                break; 
                            }
                        }
                    }
                }
            },
            cambiarPagina(page,buscar,Bestado,Bresponsable,Bparticipantes)
            {
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarProyecto(page,buscar,Bestado,Bresponsable,Bparticipantes);
            },
            listarAsocBusqueda ()
            {
                let me = this;
                var url= this.ruta +'/conf_proyectos/s_responsables';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrAsocBusqueda = respuesta.responsables;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarProyecto (page,buscar,Bestado,Bresponsable,Bparticipantes)
            {
                let me=this;
                var url= `${this.ruta}/conf_proyectos?page=${page}&leer=${this.permisosUser.leer}&buscar=${buscar}&Bestado=${Bestado}&Bresponsable=${Bresponsable}&Bparticipantes=${Bparticipantes}`;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayProyecto = respuesta.proyectos.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            validarProyecto()
            {
                this.errorProyecto = 0;
                this.errorMostrarMsjProyecto =[];

                if (!this.proyecto) this.errorMostrarMsjProyecto.push("El nombre del proyecto no puede estar vacío.");
                this.value.forEach((partici) => {
                    if (this.responsable == partici.id) {
                        this.errorMostrarMsjProyecto.push(`${partici.nombre_completo} ya esta vinculado al proyecto como responsable`);
                    }
                }, this);

                if (this.errorMostrarMsjProyecto.length) this.errorProyecto = 1;

                return this.errorProyecto;
            },
            registrarProyecto()
            {
                if (this.validarProyecto()){
                    return;
                }
                let me = this;
                axios.post(this.ruta +'/conf_proyectos/registrar',{
                    'proyecto': this.proyecto,
                    'descripcion': this.descripcion,
                    'fk_respons_users': this.responsable,
                    'correoProductOwner': this.correo_productouner,
                    'value': this.value,
                    'valueProcesos' : this.valueProcesos,
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarProyecto(1,'','','','');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarProyecto()
            {
               if (this.validarProyecto()){
                    return;
                }
                
                let me = this;

                axios.put(this.ruta +'/conf_proyectos/actualizar/'+ this.proyecto_id,{
                    'proyecto': this.proyecto,
                    'descripcion': this.descripcion,
                    'fk_respons_users': this.responsable,
                    'correoProductOwner': this.correo_productouner,
                    'value': this.value,
                    'valueProcesos' : this.valueProcesos,
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarProyecto(1,'','','','');
                }).catch(function (error) {
                    console.log(error);
                }); 
            },
            sProcesos()
            {
                let me = this;
                var url = `${this.ruta}/conf_proyectos/s_procesos`;
                axios.get(url).then(function (response) {
                    me.arrProcesos = response.data.procesos;
                })
                .catch(function (error) {
                    console.log(error);
                });

            },
            sResponsables()
            {
                let me=this;
                var url= this.ruta +'/conf_proyectos/s_responsables';
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayRespon = respuesta.responsables;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            multiSelectAsociados(id)
            {
                let me = this;
                var url= `${this.ruta}/conf_proyectos/ms_asociados/${id}`;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.value = respuesta.msAsociados;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            multiSelectProcesos(id)
            {
                let me = this;
                var url= `${this.ruta}/conf_proyectos/ms_procesos/${id}`;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.valueProcesos = respuesta.msProcesos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            asociados(id)
            {
                let me=this;
                var url= `${this.ruta}/conf_proyectos/listar_asociados/${id}`;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayPartici = respuesta.asociados;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cerrarModal()
            {
                this.modal=0;
                this.tituloModal='';
                this.proyecto='';
                this.descripcion='';
                this.responsable='';
                this.fecha_inicio='';
                this.fecha_limite='';
                this.correo_productouner='';
                this.errorProyecto=0;
                this.value = [];

            },
            cerrarModalParticipantes()
            {
                this.modalParticipantes=0;
                this.tituloModal='';
                this.proyecto='';
                this.id='';
                this.nombre_completo;
                this.arrayPartici = [];

            },
            desactivarProyecto(id)
            {
               swal({
                title: 'Esta seguro de desactivar este proyecto',
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
                    // console.log('hijos');
                    // console.log('id: '+id);
                    axios.put(me.ruta +'/conf_proyectos/desactivar/'+ id,{
                    }).then(function (response) {
                        me.listarProyecto(1,'','','','');
                        swal(
                        'Cancelado!',
                        'El proyecto ha sido cancelado con éxito.',
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
                }) 
            },
            activarProyecto(id)
            {
               swal({
                title: 'Esta seguro de activar este proyecto?',
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

                    axios.put(this.ruta +'/conf_proyectos/activar/'+ id,{
                    }).then(function (response) {
                        me.listarProyecto(1,'','','','');
                        swal(
                        'Activado!',
                        'El proyecto ha sido activado con éxito.',
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
                }) 
            },
            FinalizarProyecto(id)
            {
               swal({
                title: 'Esta seguro de finalizar este proyecto',
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
                    // console.log('hijos');
                    // console.log('id: '+id);
                    axios.put(me.ruta +'/conf_proyectos/finalizar/'+ id,{
                    }).then(function (response) {
                        me.listarProyecto(1,'','','','');
                        swal(
                        'Finalizado!',
                        'El proyecto ha sido cancelado con éxito.',
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
                }) 
            },
        },
        mounted() {
            this.listarProyecto(1,this.buscar,this.Bestado,this.Bresponsable,this.Bparticipantes);
            this.listarAsocBusqueda();
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