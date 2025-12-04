// noinspection JSUnresolvedReference,JSUnusedGlobalSymbols

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

    function toggleConnection(conn) {
        if (connections.value.includes(conn)) {
            connections.value = connections.value.filter(c => c !== conn);

            if (defaultConnection.value === conn) {
                defaultConnection.value = connections.value[0] || null;
            }
        } else {
            connections.value.push(conn);
        }
    }

    return {
        connections,
        defaultConnection,
        setConnections,
        setDefault,
        toggleConnection
    };
});
