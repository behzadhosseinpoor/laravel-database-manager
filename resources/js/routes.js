import AppLayout from './layout/AppLayout.vue'
import dashboard from './screens/Dashboard.vue'

export default [
    {
        path: '/',
        component: AppLayout,
        children: [
            {path: '', redirect: '/dashboard'},

            {path: 'dashboard', name: 'dashboard', component: dashboard},
        ]
    }
];
