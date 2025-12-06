// noinspection JSUnresolvedReference,JSUnusedGlobalSymbols

export default {
    computed: {
        DatabaseManager() {
            return DatabaseManager;
        },
    },

    methods: {
        humanSize(bytes) {
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) {
                bytes /= 1024;
                i++;
            }
            return bytes.toFixed(2) + ' ' + units[i];
        },
    },
};