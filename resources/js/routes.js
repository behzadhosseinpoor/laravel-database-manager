import AppLayout from './layout/AppLayout.vue'
import Overview from "./screens/Overview.vue";

export default [
    {
        path: '/:connection?',
        component: AppLayout,
        props: true,
        children: [
            {
                path: '',
                name: 'overview',
                component: Overview,
                meta: {title: 'Overview'}
            },
        ]
    }
];