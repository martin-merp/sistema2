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
                        <i class="fa fa-align-justify"></i> Tarifarios
                        <button v-if="permisosUser.crear && ver==0" type="button" @click="abrirModal('tarifario','registrar')" class="btn btn-primary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button v-else type="button" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body" v-if="ver==0">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select v-if="permisosUser.leer" class="form-control col-md-3" v-model="criterio">
                                      <option value="nombre">Nombre</option>>
                                    </select>
                                    <select v-else disabled class="form-control col-md-3" v-model="criterio">
                                    </select>

                                    <input v-if="permisosUser.leer" type="text" v-model="buscar" @keyup.enter="listarTarifario(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <input v-else disabled type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">

                                    <button v-if="permisosUser.leer" type="submit" @click="listarTarifario(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    <button v-else type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="col-md-5">Nombre</th>
                                    <th class="col-md-5">descripcion</th>
                                    <th class="col-md-2">Opciones</th>
                                </tr>
                            </thead>
                            <tbody v-if="permisosUser.leer">
                                <tr v-for="tarifario in arrayTarifario" :key="tarifario.id">
                                    <td v-text="tarifario.nombre"></td>
                                    <td v-text="tarifario.descripcion"></td>
                                    <td>
                                        <button v-if="permisosUser.actualizar && tarifario.estado" type="button" @click="abrirModal('tarifario','actualizar',tarifario)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <button v-else type="button" class="btn btn-secondary btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button>

                                        <button v-if="permisosUser.leer" type="button" @click="abrirModal('tarifario','ver',tarifario)" class="btn btn-success btn-sm">
                                          <i class="icon-eye"></i>
                                        </button>
                                        <button v-else type="button" class="btn btn-secondary btn-sm">
                                          <i class="icon-eye"></i>
                                        </button>

                                        <template v-if="permisosUser.anular">
                                            <button v-if="tarifario.estado" type="button" class="btn btn-danger btn-sm" @click="desactivarTarifario(tarifario.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button v-else type="button" class="btn btn-info btn-sm" @click="activarTarifario(tarifario.id)">
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button v-if="tarifario.estado" type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button v-else type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>                                
                            </tbody>
                            <tbody v-else>
                                <tr><td colspan="3">No hay registros para mostrar</td></tr>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="card-body" v-if="ver==1">
                        <h4 v-text="verTarifario"></h4><br><br>
                        <h6>Descripción:</h6><br>
                        <p v-text="verDescripcionTarifario"></p><br>
                        
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="productosTarifario in arrayProductosTarifario" :key="productosTarifario.id">
                                    <td v-text="productosTarifario.nom_articulo"></td>
                                    <td v-text="productosTarifario.valor"></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
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
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                                        <div class="col-md-9">
                                            <input type="text" v-model="nombre" class="form-control" placeholder="Nombre del tarifario">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-md-3 form-control-label" for="text-input">descripcion</label>
                                        <div class="col-md-9">
                                            <textarea v-model="descripcion" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row" v-if="tipoAccion==1">
                                    <div class="col-md-6">
                                        <label class="col-md-3 form-control-label" for="text-input">Traer de ...</label>
                                        <div class="col-md-9">
                                            <select class="form-control" v-model="idTraerDeTarifario" @change="traerDe(idTraerDeTarifario)">
                                                <option value="0">Seleccione</option>
                                                <option v-for="tarifario in arraySelectTarifario" :key="tarifario.id" :value="tarifario.id" v-text="tarifario.nombre"></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>

                            </form>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-8">Nombre</th>
                                                    <th class="col-md-4">Valor</th>
                                                    <!--<th>Opciones</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="productosTarifario in arrayProductosTarifario" :key="productosTarifario.id">
                                                    <td v-text="productosTarifario.nom_articulo"></td>
                                                    <td><input type="number" v-model="productosTarifario.valor"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div v-show="errorTarifario" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjTarifario" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarTarifario()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarTarifario()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
        </main>
</template>

<script>
    export default {
        props : ['ruta', 'permisosUser'],
        data (){
            return {
                tarifario_id: 0,
                nombre : '',
                descripcion: '',
                idTraerDeTarifario : 0,
                arrayTarifario : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorTarifario : 0,
                errorMostrarMsjTarifario : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 10,
                criterio : 'nombre',
                buscar : '',

                arrayArticulos : [],
                arrayProductosTarifario : [],
                arraySelectTarifario: [],

                ver : 0,
                verTarifario: '',
                verDescripcionTarifario: '',
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
            listarTarifario (page,buscar,criterio){
                let me=this;
                var url= this.ruta +'/con_tarifario?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayTarifario = respuesta.tarifario.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            selectTarifario(page,buscar,criterio){
                let me=this;
                var url= this.ruta +'/con_tarifario?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arraySelectTarifario = respuesta.tarifario.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarArticulos(page,buscar,criterio){
                let me=this;
                var url= this.ruta +'/articulo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayArticulos = respuesta.articulos.data;

                    for(var i=0; i<me.arrayArticulos.length; i++)
                    {
                        me.arrayProductosTarifario.push({
                            'id_producto' : me.arrayArticulos[i].id,
                            'nom_articulo' : me.arrayArticulos[i].nombre,
                            'valor' : 0,
                        });
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarProductosTarifario(id_tarifario){
                let me=this;
                var url= this.ruta +'/producto_tarifario/selectProductoTarifario?id_tarifario=' + id_tarifario;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayProductosTarifario = respuesta.producto_tarifario;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            traerDe(id){
                if(id!=0)
                {
                    this.listarProductosTarifario(id);
                }
                else
                {
                    this.listarArticulos('','','');
                }
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarTarifario(page,buscar,criterio);
            },
            registrarTarifario(){
                if (this.validarTarifario()){
                    return;
                }
                
                let me = this;

                axios.post(this.ruta +'/con_tarifario/registrar',{
                    'nombre': this.nombre,
                    'descripcion': this.descripcion,
                    'arrayProductosTarifario' : this.arrayProductosTarifario,
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarTarifario(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarTarifario(){
               if (this.validarTarifario()){
                    return;
                }
                
                let me = this;

                axios.put(this.ruta +'/con_tarifario/actualizar',{
                    'nombre': this.nombre,
                    'descripcion': this.descripcion,
                    'arrayProductosTarifario' : this.arrayProductosTarifario,
                    'id': this.tarifario_id
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarTarifario(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                }); 
            },
            validarTarifario(){
                this.errorTarifario=0;
                this.errorMostrarMsjTarifario =[];

                if (!this.nombre) this.errorMostrarMsjTarifario.push("El nombre de la presentación no puede estar vacío.");

                if (this.errorMostrarMsjTarifario.length) this.errorTarifario = 1;

                return this.errorTarifario;
            },
            desactivarTarifario(id){
               swal({
                title: 'Esta seguro de desactivar este tarifario?',
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

                    axios.put(this.ruta +'/con_tarifario/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarTarifario(1,'','nombre');
                        swal(
                        'Desactivado!',
                        'El registro ha sido desactivado con éxito.',
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
            activarTarifario(id){
               swal({
                title: 'Esta seguro de activar este tarifario?',
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

                    axios.put(this.ruta +'/con_tarifario/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarTarifario(1,'','nombre');
                        swal(
                        'Activado!',
                        'El registro ha sido activado con éxito.',
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
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.tarifario_id = 0;
                this.nombre='';
                this.descripcion='';
                this.idTraerDeTarifario=0;
                this.arrayArticulos = [];
                this.arrayProductosTarifario = [];

                this.ver=0;
                this.verTarifario = '';
                this.verDescripcionTarifario = '';
            },
            abrirModal(modelo, accion, data = []){
                this.selectTarifario(1,'','');
                switch(modelo){
                    case "tarifario":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar tarifario';
                                this.nombre= '';
                                this.descripcion = data['descripcion'];
                                this.tipoAccion = 1;
                                this.listarArticulos('','','');
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar tarifario';
                                this.tipoAccion=2;
                                this.tarifario_id=data['id'];
                                this.nombre = data['nombre'];
                                this.descripcion = data['descripcion'];
                                this.listarProductosTarifario(data['id']);
                                break;
                            }
                            case 'ver':
                            {
                                this.ver=1;
                                this.verTarifario = data['nombre']
                                this.verDescripcionTarifario = data['descripcion'];
                                this.listarProductosTarifario(data['id']);
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarTarifario(1,this.buscar,this.criterio);
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
