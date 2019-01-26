
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

require('spark-bootstrap');
require('./components/bootstrap');

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import VueRouter from 'vue-router'
import Router from './router'

Vue.use(BootstrapVue);
Vue.use(VueRouter);

const router = new VueRouter({
    mode:'history',
    routes: Router
});

const app = new Vue({
    router,
    mixins: [require('spark')]
});
