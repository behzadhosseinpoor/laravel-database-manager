// noinspection JSUnresolvedReference

import axios from 'axios';
import {createApp} from 'vue';
import {createRouter, createWebHistory} from 'vue-router';
import Routes from './routes';
import Base from './base';
import App from './App.vue';
import {createPinia} from 'pinia';

let token = document.head.querySelector("meta[name='csrf-token']");

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const app = createApp(App);

app.config.globalProperties.$http = axios.create();

window.DatabaseManager.basePath = window.DatabaseManager.path;

let routerBasePath = window.DatabaseManager.basePath + '/';

if (window.DatabaseManager.path === '' || window.DatabaseManager.path === '/') {
    routerBasePath = '/';
    window.DatabaseManager.basePath = '';
}

const router = createRouter({
    history: createWebHistory(routerBasePath),
    routes: Routes,
});

const pinia = createPinia();

app.use(pinia);

app.use(router)

app.mixin(Base);

app.mount('#database-manager');