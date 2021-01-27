require('./bootstrap');


window.Vue = require('vue');

import '@fortawesome/fontawesome-free/css/all.css';
import 'vue-toast-notification/dist/theme-sugar.css';
import '@fortawesome/fontawesome-free/js/all.js';

// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //


// Dados globais para construção da aplicação
import {LAYOUT} from './global/layout.js';
import {PREFERENCES} from './global/preferences.js';
import VueToast from 'vue-toast-notification';
import Vuetify from 'vuetify';


// Declaração de dados globais para a aplicação
Vue.prototype.LAYOUT        =   LAYOUT;
Vue.prototype.PREFERENCES   =   PREFERENCES;
Vue.use(VueToast);
Vue.use(Vuetify);


// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //

Vue.component('passport-clients',require('./components/passport/Clients.vue').default);
Vue.component('passport-authorized-clients',require('./components/passport/AuthorizedClients.vue').default);
Vue.component('passport-personal-access-tokens',require('./components/passport/PersonalAccessTokens.vue').default);

// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //


Vue.component('filter-app', require('./components/layout/filter.vue').default);
Vue.component('logo', require('./components/layout/logo.vue').default);
Vue.component('menu-app', require('./components/layout/menu.vue').default);
Vue.component('login', require('./components/auth/login.vue').default);
Vue.component('performance', require('./components/performance/performance.vue').default);
Vue.component('chart-axont', require('./components/performance/chartAxonT.vue').default);


// -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- # -- //


const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
});
