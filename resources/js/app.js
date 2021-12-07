/* eslint-disable no-undef,no-multi-spaces */
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Vue from 'vue'                               // https://vuejs.org/v2/guide/installation.html
import VueRouter from 'vue-router'                  // https://router.vuejs.org/installation.html
import Buefy from 'buefy'                           // https://buefy.org/documentation/start/

require('./bootstrap')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('AuthHero', require('./components/common/AuthHero.vue').default)
Vue.component('Setup', require('./components/Setup.vue').default)
Vue.component('Login', require('./components/Login.vue').default)
Vue.component('Dashboard', require('./components/Dashboard.vue').default)
Vue.component('ExampleComponent', require('./components/ExampleComponent.vue').default)

Vue.use(VueRouter)
export const router = new VueRouter({
    mode: 'history',
    routes: []
})

Vue.use(Buefy)

// eslint-disable-next-line no-unused-vars
const app = new Vue({
    el: '#app',
    router: router
})
