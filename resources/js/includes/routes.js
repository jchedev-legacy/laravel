import {redirectTo404} from '~/includes/helpers';

import Layout from '~/pages';
import Home from '~/pages/Home';

const routes = [
    {
        path:      '/',
        component: Layout,
        children:  [
            {
                path:      '/',
                component: Home
            }
        ]
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