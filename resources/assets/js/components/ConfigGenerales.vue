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
                        <i class="fa fa-align-justify"></i> Configuraciones generales
                        <button v-if="permisosUser.crear" type="button" @click="abrirModal('configgenerales','registrar')" class="btn btn-primary" title="Buscar">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button v-else type="button" class="btn btn-secondary" title="Buscar">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select v-if="permisosUser.crear" class="form-control col-md-3" v-model="criterio">
                                      <option value="nombre">Nombre</option>>
                                    </select>
                                    <select v-else disabled class="form-control col-md-3" v-model="criterio">
                                      <option value="nombre">Nombre</option>>
                                    </select>

                                    <input v-if="permisosUser.leer" type="text" v-model="buscar" @keyup.enter="listarConfigGenerales(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar" title="Texto a buscar">
                                    <input v-else disabled type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar" title="Texto a buscar">

                                    <button v-if="permisosUser.leer" type="submit" @click="listarConfigGenerales(1,buscar,criterio)" class="btn btn-primary" title="Buscar"><i class="fa fa-search"></i> Buscar</button>
                                    <button v-else type="submit" class="btn btn-secondary" title="Buscar"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th class="col-md-1">Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="configgenerales in arrayConfigGenerales" :key="configgenerales.id">
                                    <td v-text="configgenerales.nombre"></td>
                                    <td v-text="configgenerales.logo"></td>
                                    <td v-text="configgenerales.repre_legal"></td>
                                    <td v-text="configgenerales.nit"></td>
                                    <td v-text="configgenerales.direccion"></td>
                                    <td v-text="configgenerales.res_fact_elect"></td>
                                    <td v-text="configgenerales.res_fact_pos"></td>
                                    <td v-text="configgenerales.correo"></td>
                                    <td v-text="configgenerales.celular"></td>
                                    <td v-text="configgenerales.telefono"></td>
                                    <td>
                                        <button v-if="permisosUser.actualizar" type="button" @click="abrirModal('configgenerales','actualizar',configgenerales)" class="btn btn-warning btn-sm" title="Editar">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <button v-else type="button" class="btn btn-secondary btn-sm" title="Editar">
                                          <i class="icon-pencil"></i>
                                        </button> &nbsp;
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
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="col-md-1 form-control-label float-left" for="text-input">Nombre</label>
                                        <div class="col-md-11 float-right">
                                            <input type="text" v-model="nombre" style="width: 96%;" class="form-control float-right" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Logo</label>
                                        <div class="col-md-9 float-right">
                                            <input type="file" id="logo" name="logo" @change="cargarLogo" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Rep. legal</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="repre_legal" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">NIT</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="nit" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Direccion</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="direccion" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Res. facturacion electronica</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="res_fact_elect" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Res. factura POS</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="res_fact_pos" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Correo</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="correo" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Celular</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="celular" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="col-md-3 form-control-label float-left">Telefono</label>
                                        <div class="col-md-9 float-right">
                                            <input type="text" v-model="telefono" class="form-control" placeholder="Nombre de la empresa">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                    </div>
                                </div>
                                <div v-show="errorConcentracion" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjConcentracion" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarConfigGeneral()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarConfigGeneral()">Actualizar</button>
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
                concentracion_id: 0,
                nombre : '',
                arrayConcentracion : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorConcentracion : 0,
                errorMostrarMsjConcentracion : [],
                
                configgenerales_id: 0,
                nombre : '',
                logo: '',
                arrayLogo: '',
                repre_legal: '',
                nit: '',
                direccion: '',
                res_fact_elect: '',
                res_fact_pos: '',
                correo: '',
                celular: '',
                telefono: '',
                arrayConfigGenerales : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorConfigGenerales : 0,
                errorMostrarMsjConfigGenerales : [],

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
            listarConfigGenerales (page,buscar,criterio){
                let me=this;
                var url= this.ruta +'/configgenerales?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayConfigGenerales = respuesta.configgenerales.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cargarLogo(event){
                let me=this;
                me.arrayLogo = event.target.files[0];
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarConfigGenerales(page,buscar,criterio);
            },
            registrarConfigGeneral(){
                // if (this.validarConcentracion()){
                //     return;
                // }
                
                let me = this;
                var data = new FormData();
                data.append('nombre', this.nombre);
                data.append('logo', this.arrayLogo);
                data.append('repre_legal', this.repre_legal);
                data.append('nit', this.nit);
                data.append('direccion', this.direccion);
                data.append('res_fact_elect', this.res_fact_elect);
                data.append('res_fact_pos', this.res_fact_pos);
                data.append('correo', this.correo);
                data.append('celular', this.celular);
                data.append('telefono', this.telefono);

                axios.post(this.ruta +'/configgenerales/registrar', data,{
                    headers:{'Content-Type':'multipart/form-data'}
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarConfigGenerales(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarConfigGeneral(){
                // if (this.validarConcentracion()){
                //     return;
                // }

                let me = this;
                
                var data2 = new FormData();
                data2.append('nombre', this.nombre);
                data2.append('logo', this.arrayLogo);
                data2.append('repre_legal', this.repre_legal);
                data2.append('nit', this.nit);
                data2.append('direccion', this.direccion);
                data2.append('res_fact_elect', this.res_fact_elect);
                data2.append('res_fact_pos', this.res_fact_pos);
                data2.append('correo', this.correo);
                data2.append('celular', this.celular);
                data2.append('telefono', this.telefono);
                data2.append('id', this.configgenerales_id);

                axios.post(this.ruta +'/configgenerales/actualizar', data2,{
                    headers:{'Content-Type':'multipart/form-data'}
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarConfigGenerales(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            validarConcentracion(){
                this.errorConcentracion=0;
                this.errorMostrarMsjConcentracion =[];

                if (!this.nombre) this.errorMostrarMsjConcentracion.push("El nombre de la presentación no puede estar vacío.");

                if (this.errorMostrarMsjConcentracion.length) this.errorConcentracion = 1;

                return this.errorConcentracion;
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.nombre='';
                this.logo= '';
                this.arrayLogo = null;
                this.repre_legal= '';
                this.nit= '';
                this.direccion= '';
                this.res_fact_elect= '';
                this.res_fact_pos= '';
                this.correo= '';
                this.celular= '';
                this.telefono= '';
            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "configgenerales":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar configuraciones generales';
                                this.nombre= '';
                                this.logo = '';
                                this.repre_legal = '';
                                this.nit = '';
                                this.direccion = '';
                                this.res_fact_elect = '';
                                this.res_fact_pos = '';
                                this.correo = '';
                                this.celular = '';
                                this.telefono = '';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                // console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar configuraciones generales';
                                this.tipoAccion=2;
                                this.configgenerales_id=data['id'];
                                this.nombre = data['nombre'];
                                this.logo = data['logo'];
                                this.repre_legal = data['repre_legal'];
                                this.nit = data['nit'];
                                this.direccion = data['direccion'];
                                this.res_fact_elect = data['res_fact_elect'];
                                this.res_fact_pos = data['res_fact_pos'];
                                this.correo = data['correo'];
                                this.celular = data['celular'];
                                this.telefono = data['telefono'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarConfigGenerales(1,this.buscar,this.criterio);
        }
    }
</script>
<style>    
    .modal-content{
        width: 120% !important;
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
