
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import Multiselect from 'vue-multiselect'  

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('rol', require('./components/Rol.vue'));
Vue.component('user', require('./components/User.vue'));
Vue.component('terceros', require('./components/Terceros.vue'));
Vue.component('modulo', require('./components/Modulo.vue'));
Vue.component('plancuentas', require('./components/PlanCuentas.vue'));
Vue.component('conformatos', require('./components/ConFormatos.vue'));
Vue.component('formatos', require('./components/Formatos.vue'));
Vue.component('registroconta', require('./components/RegistroConta.vue'));
Vue.component('configgenerales', require('./components/ConfigGenerales.vue'));
Vue.component('auxiliares_conta', require('./components/AuxiliaresConta.vue'));
Vue.component('retenciones', require('./components/Retenciones.vue'));
Vue.component('colaboradores', require('./components/Colaboradores.vue'));
Vue.component('zona', require('./components/Zona.vue'));
Vue.component('bancos', require('./components/Bancos.vue'));
Vue.component('facturacion', require('./components/Facturacion.vue'));
Vue.component('articulo', require('./components/Articulo.vue'));
Vue.component('categoria', require('./components/Categoria.vue'));
Vue.component('categoria2', require('./components/Categoria2.vue'));
Vue.component('presentacion', require('./components/Presentacion.vue'));
Vue.component('und_medida', require('./components/UndMedida.vue'));
Vue.component('concentracion', require('./components/Concentracion.vue'));
Vue.component('stock', require('./components/Stock.vue'));
Vue.component('ingreso', require('./components/Ingreso.vue'));
Vue.component('egreso', require('./components/Egreso.vue'));
Vue.component('cliente', require('./components/Cliente.vue'));
Vue.component('con_tarifario', require('./components/ConTarifario.vue'));


Vue.component('proceso', require('./components/Proceso.vue'));
Vue.component('conf_proyectos', require('./components/ConfProyectos.vue'));
Vue.component('cargo', require('./components/Cargo.vue'));
Vue.component('plan_accion', require('./components/PlanAccion.vue'));
Vue.component('conf_indicadores', require('./components/ConfIndicadores.vue'));
Vue.component('multiselect', Multiselect);

const app = new Vue({
    el: '#app'
    , data :{
        menu : 0
        //, ruta : 'http://192.168.100.231/sistema2/public'
        , ruta : 'http://localhost/sistema2/public'
        , permisosUser : []
        , axiosApp : axios.create({baseURL : 'http://localhost/sistema2/public'})
    }
    , mounted() {
        let me = this;
        var url= this.ruta +'/permisos/listarPermisosLogueado';
        axios.get(url).then(function (response) {
            me.permisosUser = response.data.permisosLogueado;
        })
        .catch(function (error) {
            console.log(error);
        });
    }
});
