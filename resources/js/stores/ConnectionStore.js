// noinspection JSUnresolvedReference

import {defineStore} from 'pinia';

export const useConnectionStore = defineStore('connection', {
    state: () => ({
        connections: [],
        defaultConnection: null,
    }),

    actions: {
        setConnections(list) {
            this.connections = list;

            if (!this.defaultConnection && list.length > 0) {
                this.defaultConnection = list[0];
            }
        },

        setDefault(conn) {
            if (this.connections.includes(conn)) {
                this.defaultConnection = conn;
            }
        }
    }
});

