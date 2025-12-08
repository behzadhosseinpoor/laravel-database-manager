import {defineStore} from 'pinia';

export const useUiStore = defineStore('ui', {
    state: () => ({
        ready: true,
    }),

    actions: {
        showLoading() {
            this.ready = false;
        },

        hideLoading() {
            this.ready = true;
        }
    }
});
