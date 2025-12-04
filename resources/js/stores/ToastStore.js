// noinspection JSUnresolvedReference

import {defineStore} from 'pinia';

export const useToastStore = defineStore('toast', {
    state: () => ({
        toasts: [],
    }),

    actions: {
        show(message, type = 'success', timeout = 5000) {
            const id = Date.now();

            this.toasts.push({id, message, type});

            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, timeout);
        },

        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }
});
