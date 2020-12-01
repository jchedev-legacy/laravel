import {redirectTo404} from '~/includes/helpers';

import Layout from '~/pages';

const routes = [
    {
        path:      '/',
        component: Layout
    },
    {
        name:      '404',
        path:      "*",
        component: function() {
            redirectTo404();
        }
    }
];

export {
    routes as default
}