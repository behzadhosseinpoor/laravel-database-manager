<!--suppress JSUnresolvedReference -->
<template>
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                bg-purple-500/10 text-purple-600 dark:text-purple-300
                dark:bg-purple-500/20 text-2xl">
                <i class="fa-solid fa-table"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    Tables
                    <span
                        class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                        :title="'Connection: ' + connection">
            {{ connection }}
          </span>
                </h1>

                <p class="text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ tables.length }} tables found
                </p>
            </div>
        </div>
    </div>

    <DataTable :key="tableKey" :data="tables" :columns="columns" :showActions="true" :useQueryParams="false">
        <template #actions="{ row }">
            <div class="flex items-center gap-2 justify-center">
                <!-- Browse -->
                <button
                    @click="browse(row)"
                    class="px-3 py-1.5 rounded-md font-medium flex items-center gap-1
        shadow-sm hover:shadow-md transition cursor-pointer
        text-white bg-blue-600 hover:bg-blue-700
        dark:bg-blue-700 dark:hover:bg-blue-800">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Browse
                </button>

                <!-- Structure -->
                <button
                    @click="structure(row)"
                    class="px-3 py-1.5 rounded-md font-medium flex items-center gap-1
        shadow-sm hover:shadow-md transition cursor-pointer
        text-white bg-purple-600 hover:bg-purple-700
        dark:bg-purple-700 dark:hover:bg-purple-800">
                    <i class="fa-solid fa-diagram-project"></i>
                    Structure
                </button>

                <!-- Truncate -->
                <button
                    @click="openTruncate(row)"
                    class="px-3 py-1.5 rounded-md font-medium flex items-center gap-1
        shadow-sm hover:shadow-md transition cursor-pointer
        text-white bg-yellow-500 hover:bg-yellow-600
        dark:bg-yellow-600 dark:hover:bg-yellow-700">
                    <i class="fa-solid fa-eraser"></i>
                    Truncate
                </button>

                <!-- Drop -->
                <button
                    @click="openDrop(row)"
                    class="px-3 py-1.5 rounded-md font-medium flex items-center gap-1
        shadow-sm hover:shadow-md transition cursor-pointer
        text-white bg-red-600 hover:bg-red-700
        dark:bg-red-700 dark:hover:bg-red-800">
                    <i class="fa-solid fa-trash"></i>
                    Drop
                </button>
            </div>
        </template>
    </DataTable>
</template>

<script>
import {watch} from "vue";
import {useUiStore} from '../stores/UiStore';
import DataTable from "../components/DataTable.vue";
import TableActionModal from "../components/modals/TableActionModal.vue";
import {useRoute, useRouter} from "vue-router";
import {useModalStore} from "../stores/ModalStore.js";
import {useToastStore} from "../stores/ToastStore.js";
import api from "../libraries/api.js";

export default {
    components: {DataTable, TableActionModal},

    data() {
        return {
            connection: null,
            tables: [],
            columns: [
                {field: 'name', header: 'Name', sortable: true},
                {field: 'rows', header: 'Rows', sortable: true},
                {field: 'engine', header: 'Engine', sortable: true},
                {field: 'collation', header: 'Collation', sortable: true},
                {
                    field: 'size',
                    header: 'Size',
                    sortable: true,
                    format: (value) => this.humanSize(value)
                },
            ]
        };
    },

    mounted() {
        this.connection = this.route.params.connection;

        this.updateTitle();
        this.getData();

        watch(
            () => this.route.params.connection,
            (newConn) => {
                if (!newConn) return;

                this.connection = newConn;

                this.updateTitle();
                this.getData();
            }
        );
    },

    methods: {
        async loadTables(conn) {
            const res = await api.get(`/${DatabaseManager.basePath}/api/${conn}/tables`);

            this.tables = res.data.result;
        },

        browse(row) {
            this.router.push({
                name: "table-browse",
                params: {table: row.name, connection: this.connection}
            })
        },

        structure(row) {
            this.router.push({
                name: "table-structure",
                params: {table: row.name, connection: this.connection}
            })
        },

        openTruncate(row) {
            this.modal.open(TableActionModal, {
                table: row.name,
                action: "truncate"
            }).then((res) => {
                if (res?.confirmed) {
                    this.handleTableAction({
                        request: () => api.post(
                            `/${DatabaseManager.basePath}/api/${this.connection}/tables/${row.name}/truncate`,
                            {
                                "foreign_key_check": res.enableForeignKeys,
                            }
                        ),
                    });
                }
            })
        },

        openDrop(row) {
            this.modal.open(TableActionModal, {
                table: row.name,
                action: "drop"
            }).then((res) => {
                if (res?.confirmed) {
                    this.handleTableAction({
                        request: () => api.post(
                            `/${DatabaseManager.basePath}/api/${this.connection}/tables/${row.name}/drop`,
                            {
                                "foreign_key_check": res.enableForeignKeys,
                            }
                        ),
                    });
                }
            })
        },

        updateTitle() {
            document.title = "Database Manager - " + this.connection + " - Tables";
        },

        async getData() {
            try {
                this.ui.showLoading();

                await this.loadTables(this.connection);
            } finally {
                this.ui.hideLoading();
            }
        },

        async handleTableAction({request}) {
            try {
                this.ui.showLoading();

                await request();

                await this.loadTables(this.connection);
            } finally {
                this.ui.hideLoading();
            }
        }
    },

    setup() {
        return {
            route: useRoute(),
            router: useRouter(),
            ui: useUiStore(),
            toast: useToastStore(),
            modal: useModalStore(),
        };
    },

    computed: {
        tableKey() {
            return JSON.stringify({
                columns: this.columns,
                connection: this.connection,
                table: this.table,
                rows: this.tables.length,
            });
        }
    }
}
</script>
