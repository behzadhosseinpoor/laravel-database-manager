// noinspection JSUnresolvedReference

import axios from 'axios';
import {useToastStore} from '../stores/ToastStore.js';

const api = axios.create();

api.interceptors.response.use(
    (response) => {
        const toast = useToastStore();

        if (response.data && response.data.message) {
            toast.show(response.data.message, 'success');
        }

        return response;
    },
    (error) => {
        const toast = useToastStore();

        if (error.response) {
            const data = error.response.data;

            if (data && data.message) {
                toast.show(data.message, 'error');
            } else {
                toast.show('Unexpected error occurred.', 'error');
            }

        } else if (error.request) {
            toast.show('No response from server.', 'error');
        } else {
            toast.show(error.message, 'error');
        }

        return Promise.reject(error);
    }
);

export default api;
