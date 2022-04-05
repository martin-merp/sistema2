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
                        <i class="fa fa-align-justify"></i> Cargos
                        <button type="button" @click="abrirModal('cargos','registrar')" class="btn btn-primary" v-if="permisosUser.crear">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button type="button" class="btn btn-secondary" v-else>
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                      <option value="cargo">Cargo</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup.enter="listarCargo(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarCargo(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Cargo</th>
                                    <th>Descripcion</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cargo in arrayCargo" :key="cargo.id">
                                    <td v-text="cargo.cargo"></td>
                                    <td v-text="cargo.descripcion"></td>
                                    <td>
                                        <div v-if="cargo.estado">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Inactivo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" @click="abrirModal('cargos','actualizar',cargo)" class="btn btn-warning btn-sm" title="Editar" v-if="cargo.estado && permisosUser.actualizar">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" v-else>
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="permisosUser.anular">
                                            <button type="button" class="btn btn-danger btn-sm" @click="actualizarEstado(cargo.estado, 'este cargo', cargo.id)" title="Anular" v-if="cargo.estado">
                                                <i class="icon-trash"></i>
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" @click="actualizarEstado(cargo.estado, 'este cargo', cargo.id)" title="Activar" v-else>
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-secondary btn-sm">
                                                <i class="icon-trash" v-if="cargo.estado"></i>
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
                                    <label class="col-md-3 form-control-label" for="text-input">Cargo (*)</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="cargo" class="form-control" placeholder="Cargo del empleado">                                        
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label" for="text-input">Descripción</label>
                                    <div class="col-md-9">
                                        <input type="text" v-model="descripcion" class="form-control" placeholder="Descripción del cargo">                                        
                                    </div>
                                </div>
                                <div v-show="errorCargo" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjCargo" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCargo()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarCargo()">Actualizar</button>
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
        props : ['permisosUser', 'ruta'],
        data (){
            return {
                url : `${this.ruta}/cargos`,
                
                cargo_id: 0,
                cargo : '',
                descripcion : '',
                arrayCargo : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorCargo : 0,
                errorMostrarMsjCargo : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'nombre',
                buscar : ''
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
            abrirModal(modelo, accion, data = [])
            {
                switch(modelo){
                    case "cargos":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Cargo ';
                                this.cargo= '';
                                this.descripcion='';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar Cargo';
                                this.tipoAccion=2;
                                this.cargo_id=data['id'];
                                this.cargo = data['cargo'];
                                this.descripcion = data['descripcion'];
                                break;
                            }
                        }
                    }
                }
            },
            cambiarPagina(page,buscar,criterio)
            {
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarCargo(page,buscar,criterio);
            },
            listarCargo (page,buscar,criterio)
            {
                let me=this;
                axios.get(`${this.url}?page=${page}&leer=${this.permisosUser.leer}&buscar=${buscar}&criterio=${criterio}`).then(function (response) {
                    var respuesta= response.data;
                    me.arrayCargo = respuesta.cargos.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            validarCargo()
            {
                this.errorCargo=0;
                this.errorMostrarMsjCargo =[];
                if (!this.cargo) this.errorMostrarMsjPersona.push("El nombre del cargo no puede estar vacío.");
                if (this.errorMostrarMsjCargo.length) this.errorCargo = 1;
                return this.errorCargo;
            },
            registrarCargo()
            {
                if (this.validarCargo()){
                    return;
                }
                let me = this;
                axios.post(`${this.url}/registrar`,{
                    'cargo': this.cargo,
                    'descripcion': this.descripcion
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarCargo(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarCargo()
            {
               if (this.validarCargo()){
                    return;
                }
                let me = this;
                axios.put(`${this.url}/actualizar/${this.cargo_id}`,{
                    'cargo': this.cargo,
                    'descripcion': this.descripcion
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarCargo(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                }); 
            },
            cerrarModal()
            {
                this.modal=0;
                this.tituloModal='';
                this.cargo='';
                this.descripcion='';
                this.errorCargo=0;

            },
            actualizarEstado(estado, finalTitle, id)
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
                    var url = `${this.url}/${prefijo}activar/${id}`;
                    axios.patch(url, {
                    }).then(function (response) {
                        me.listarCargo(1,'','nombre');
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
        },
        mounted() {
            this.listarCargo(1,this.buscar,this.criterio);
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