// noinspection JSUnresolvedReference,JSUnusedGlobalSymbols

import AppLayout from './layout/AppLayout.vue'
import Overview from "./screens/Overview.vue";
import Tables from "./screens/Tables.vue";
import TableBrowse from "./screens/TableBrowse.vue";
import TableStructure from "./screens/TableStructure.vue";

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
            },
            {
                path: 'tables/:table',
                children: [
                    {
                        path: "browse",
                        name: "table-browse",
                        component: TableBrowse,
                        meta: {title: 'Browse'}
                    },
                    {
                        path: "structure",
                        name: "table-structure",
                        component: TableStructure,
                        meta: {title: 'Structure'}
                    }
                ],
            },
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
