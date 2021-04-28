<<<<<<< HEAD
import qs from "qs";
import VueRouter from "vue-router";

import routes from "~/includes/routes";
import store from "~/includes/store";

require('bootstrap');

window._ = require('lodash');

window.Popper = require('popper.js').default;

window.$ = window.jQuery = require('jquery');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Instantiate all the code around Vue since it is the library used for the front-end
 */

window.Vue = require('vue');

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
            mode:   'history',
            parseQuery(query) {
                return qs.parse(query);
            },
            stringifyQuery(query) {
                let result = qs.stringify(query);

                return result ? ('?' + result) : '';
            },
        }),
        store:  store,
    });
}
=======
require('./bootstrap');
>>>>>>> laravel/8.x
