import $ from 'jquery';
import Vue from 'vue';
import VueNoty from 'vuejs-noty';
import axios from 'axios';
import Vuelidate from 'vuelidate';
import Paginate from 'vuejs-paginate';
import * as VueGoogleMaps from 'vue2-google-maps';
import VModal from 'vue-js-modal';
window.$ = window.jQuery = $;
window.axios = axios;
require('bootstrap');
Vue.use(Vuelidate);
Vue.use(VueNoty, {
    progressBar: false,
    layout: 'bottomRight',
    theme: 'bootstrap-v4',
    timeout: 3000
});

Vue.prototype.$eventHub = new Vue();
Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyAyuUD7Us8kNM3pLyk1Fmj7Jk0t5kyCSWY',
        libraries: 'places', // This is required if you use the Autocomplete plugin
    },
});
Vue.use(VModal, { dynamic: true, injectModalsContainer: true });
import router from './router';
import store from './store/index';
import App from './components/App.vue';
import jwtToken from './helpers/jwt-token';
import Datepicker from 'vuejs-datepicker';
import 'vuejs-noty/dist/vuejs-noty.css';
import VueRouterUserRoles from "vue-router-user-roles";
Vue.use(VueRouterUserRoles, { router });


Vue.component('date-picker', Datepicker);
Vue.component('paginate', Paginate);
Vue.component('googleMap', VueGoogleMaps.Map);
Vue.component('MapMarker', VueGoogleMaps.Marker);

axios.interceptors.request.use(config => {
    config.headers['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
    config.headers['X-Requested-With'] = 'XMLHttpRequest';
    if (jwtToken.getToken()) {
        config.headers['Authorization'] = 'Bearer ' + jwtToken.getToken();
    }

    return config;
}, error => {
    return Promise.reject(error);
});

axios.interceptors.response.use(response => {
    return response;
}, error => {
    let errorResponseData = error.response.data;

    const errors = ["token_invalid", "token_expired", "token_not_provided"];

    if (errorResponseData.error && errors.includes(errorResponseData.error)) {
        store.dispatch('unsetAuthUser')
                .then(() => {
                    jwtToken.removeToken();
                    router.push({name: 'login'});
                });
    }

    return Promise.reject(error);
});

Vue.component('app', App);
   let getRole = localStorage.getItem('role');
   let currRole = '';
   if (getRole !== null) {
     currRole = getRole;
   }else{
      currRole = "guest";
   }
   let authenticate = Promise.resolve({ role: currRole});
   authenticate.then(user => {
   Vue.prototype.$user.set(user);
   const app = new Vue({
     router,
     store
   }).$mount('#app');
});