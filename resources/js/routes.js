// noinspection JSUnresolvedReference,JSUnusedGlobalSymbols

import AppLayout from './layout/AppLayout.vue'
import Overview from "./screens/Overview.vue";
import Tables from "./screens/Tables.vue";

export default [
    {
        path: '/:connection',
        component: AppLayout,
        props: true,
        children: [
            {
                path: '',
                name: 'overview',
                component: Overview,
                meta: {title: 'Overview'}
            },
            {
                path: 'tables',
                name: 'tables',
                component: Tables,
                meta: {title: 'Tables'}
            }
        ]
    },
    {
        path: '/',
        redirect: () => {
            return {
                name: 'overview',
                params: {connection: window.DatabaseManager.default_connection}
            }
        }
    }
];
