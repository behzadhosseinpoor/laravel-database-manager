import {defineStore} from 'pinia';
import {ref} from "vue";

export const useUiStore = defineStore('ui', () => {
    const ready = ref(true);

    function showLoading() {
        ready.value = false;
    }

    function hideLoading() {
        ready.value = true;
    }

    return {
        ready,
        showLoading,
        hideLoading,
    };
});
