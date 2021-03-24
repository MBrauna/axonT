require('./bootstrap');


window.Vue = require('vue');

import 'bootstrap-vue/dist/bootstrap-vue.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'vue-toast-notification/dist/theme-sugar.css';
import '@fortawesome/fontawesome-free/js/all.js';

// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //


// Dados globais para construção da aplicação
import {LAYOUT} from './global/layout.js';
import {PREFERENCES} from './global/preferences.js';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import VueToast from 'vue-toast-notification';
import Vuetify from 'vuetify';
import Vue from 'vue';
import moment from 'moment'

// Declaração de dados globais para a aplicação
Vue.prototype.LAYOUT        =   LAYOUT;
Vue.prototype.PREFERENCES   =   PREFERENCES;
Vue.prototype.moment        =   moment;
Vue.use(VueToast);
Vue.use(Vuetify);
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

Vue.component('passport-clients',require('./components/passport/Clients.vue').default);
Vue.component('passport-authorized-clients',require('./components/passport/AuthorizedClients.vue').default);
Vue.component('passport-personal-access-tokens',require('./components/passport/PersonalAccessTokens.vue').default);

// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //


Vue.component('filter-app', require('./components/layout/filter.vue').default);
Vue.component('logo', require('./components/layout/logo.vue').default);
Vue.component('menu-app', require('./components/layout/menu.vue').default);
Vue.component('alert-axont', require('./components/layout/alertAxonT.vue').default);

Vue.component('login', require('./components/auth/login.vue').default);

Vue.component('performance', require('./components/performance/performance.vue').default);
Vue.component('chart-axont', require('./components/performance/chartAxonT.vue').default);

Vue.component('create-task', require('./components/tasks/create.vue').default);
Vue.component('create-task-manual', require('./components/tasks/createManual.vue').default);
Vue.component('create-task-automatic', require('./components/tasks/createAutomatic.vue').default);
Vue.component('list-manual', require('./components/tasks/listManual.vue').default);
Vue.component('list-automatic', require('./components/tasks/listAutomatic.vue').default);


// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //


const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
});
