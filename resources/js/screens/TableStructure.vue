<!--suppress JSUnresolvedReference, JSValidateTypes -->
<template>
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                bg-purple-500/10 text-purple-600 dark:text-purple-300
                dark:bg-purple-500/20 text-2xl">
                <i class="fa-solid fa-diagram-project"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    Structure
                    <span
                        class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                        :title="'Connection: ' + connection">
            {{ connection }}
          </span>
                </h1>

                <p class="text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ table }}
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-8">

        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-6">
                <button
                    class="relative px-1 pb-3 text-sm font-medium cursor-pointer transition-colors"
                    :class="activeTab === 'columns'
          ? 'text-blue-600 dark:text-blue-400'
          : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    @click="setTab('columns')"
                >
                    Columns ({{ structure.columns.length }})

                    <span
                        class="absolute left-0 -bottom-px h-0.5 w-full rounded transition-all duration-300"
                        :class="activeTab === 'columns'
            ? 'bg-blue-600 dark:bg-blue-400'
            : 'bg-transparent'"></span>
                </button>

                <button
                    class="relative px-1 pb-3 text-sm font-medium cursor-pointer transition-colors"
                    :class="activeTab === 'indexes'
          ? 'text-blue-600 dark:text-blue-400'
          : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    @click="setTab('indexes')"
                >
                    Indexes ({{ structure.indexes.length }})

                    <span
                        class="absolute left-0 -bottom-px h-0.5 w-full rounded transition-all duration-300"
                        :class="activeTab === 'indexes'
            ? 'bg-blue-600 dark:bg-blue-400'
            : 'bg-transparent'"></span>
                </button>
            </nav>
        </div>

        <div v-if="activeTab === 'columns'">
            <DataTable :key="tableKey" :data="structure.columns" :columns="columns.columns" :useQueryParams="false"/>
        </div>

        <div v-if="activeTab === 'indexes'">
            <DataTable :key="tableKey" :data="structure.indexes" :columns="columns.indexes" :useQueryParams="false"/>
        </div>
    </div>
</template>

<script>

import {watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import {useUiStore} from "../stores/UiStore.js";
import DataTable from "../components/DataTable.vue";
import api from "../libraries/api.js";

export default {
    components: {DataTable},
    data() {
        return {
            connection: null,
            table: null,
            structure: {
                columns: [],
                indexes: [],
            },
            columns: {
                columns: [
                    {
                        field: "name",
                        header: "Name",
                        sortable: true,
                    },
                    {
                        field: "type",
                        header: "Type",
                        sortable: true,
                    },
                    {
                        field: "nullable",
                        header: "Nullable",
                        sortable: true,
                        format: (v) => (v ? "YES" : "NO")
                    },
                    {
                        field: "default",
                        header: "Default",
                        sortable: true,
                    },
                    {
                        field: "auto_increment",
                        header: "Auto Inc",
                        sortable: true,
                        format: (v) => (v ? "YES" : "NO")
                    },
                    {
                        field: "collation",
                        header: "Collation",
                        sortable: true,
                    },
                    {
                        field: "comment",
                        header: "Comment",
                        sortable: false
                    }
                ],
                indexes: [
                    {
                        field: "name",
                        header: "Index Name",
                        sortable: true,
                    },
                    {
                        field: "columns",
                        header: "Columns",
                        sortable: false,
                        format: (v) => v.join(", ")
                    },
                    {
                        field: "type",
                        header: "Type",
                        sortable: true,
                    },
                    {
                        field: "unique",
                        header: "Unique",
                        sortable: true,
                        format: (v) => (v ? "YES" : "NO")
                    },
                    {
                        field: "primary",
                        header: "Primary",
                        sortable: true,
                        format: (v) => (v ? "YES" : "NO")
                    }
                ]
            },
            activeTab: 'columns',
        };
    },

    mounted() {
        this.activeTab = this.route.query.tab ?? 'columns';

        this.connection = this.route.params.connection;
        this.table = this.route.params.table;

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

        watch(
            () => this.route.params.table,
            (newTable) => {
                if (!newTable) return;

                this.table = newTable;

                this.updateTitle();
                this.getData();
            }
        );
    },

    methods: {
        async loadStructure(conn, table) {
            const res = await api.get(`/${DatabaseManager.basePath}/api/${conn}/tables/${table}/structure`);

            this.structure = res.data.result;
        },

        async getData() {
            try {
                this.ui.showLoading();

                await this.loadStructure(this.connection, this.table);
            } finally {
                this.ui.hideLoading();
            }
        },

        updateTitle() {
            document.title =
                "Database Manager - " +
                this.connection +
                " - " +
                this.table +
                " - Structure";
        },

        setTab(tab) {
            this.activeTab = tab

            this.router.replace({
                query: {...this.route.query, tab}
            })
        }
    },

    setup() {
        return {
            route: useRoute(),
            router: useRouter(),
            ui: useUiStore()
        };
    },

    computed: {
        tableKey() {
            return JSON.stringify({
                columns: this.columns,
                connection: this.connection,
                table: this.table,
                rows: this.structure.columns.length + this.structure.indexes.length,
            });
        }
    }
}
</script>
