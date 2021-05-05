import Vue from "vue";
import VueRouter from "vue-router";
import routes from "~/includes/routes";
import store from "~/includes/store";

/**
 * What we need to enable no matter what (bootstrap, axios)
 */

require('bootstrap');

window._ = require('lodash');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.defaults.withCredentials = true;

/**
 * Instantiate all the code around Vue since it is the library used for the front-end
 */

window.Vue = Vue;

Vue.use(VueRouter);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if (document.getElementById('app')) {
    const app = new Vue({
        el:     '#app',
        router: new VueRouter({
            routes: routes,
            scrollBehavior(to, from, savedPosition) {
                return {x: 0, y: 0};
            },
            mode:   'history'
        }),
        store:  store,
    });
}