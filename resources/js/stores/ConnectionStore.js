// noinspection JSUnresolvedReference

import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useConnectionStore = defineStore('connection', () => {
    const connections = ref([]);
    const defaultConnection = ref(null);

    function setConnections(list) {
        connections.value = list;

        if (!defaultConnection.value && list.length > 0) {
            defaultConnection.value = list[0];
        }
    }

    function setDefault(conn) {
        if (connections.value.includes(conn)) {
            defaultConnection.value = conn;
        }
    }

    return {
        connections,
        defaultConnection,
        setConnections,
        setDefault,
    };
});
