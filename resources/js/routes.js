import AppLayout from './layout/AppLayout.vue'
import Dashboard from "./screens/Dashboard.vue";

export default [
    {
        path: '/:connection?',
        component: AppLayout,
        props: true,
        children: [
            {
                path: '',
                name: 'dashboard',
                component: Dashboard,
                meta: {title: 'Dashboard'}
            },
        ]
    }
];