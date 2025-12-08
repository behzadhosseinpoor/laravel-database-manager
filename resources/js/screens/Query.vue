<!--suppress JSUnresolvedReference -->
<template>
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center
                bg-purple-500/10 text-purple-600 dark:text-purple-300
                dark:bg-purple-500/20 text-2xl">
                <i class="fa-solid fa-question"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    Query
                    <span
                        class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                        :title="'Connection: ' + connection">
                        {{ connection }}
                    </span>
                </h1>

                <p class="text-gray-500 dark:text-gray-400 mt-0.5">
                    SQL Query
                </p>
            </div>
        </div>
    </div>

    <div
        v-if="collapsed"
        @click="collapsed = false"
        class="flex items-center justify-between px-4 py-2 rounded-md cursor-pointer
         bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700
         transition border border-gray-300 dark:border-gray-700">
        <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
            <i class="fa-solid fa-terminal"></i>
            <span class="font-medium text-sm">SQL Editor (click to expand)</span>
        </div>

        <i class="fa-solid fa-chevron-down"></i>
    </div>

    <transition name="collapse">
        <div v-if="!collapsed" class="overflow-hidden">
            <div class="p-4 rounded-md border bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-700">
        <textarea
            v-model="queryTemp"
            class="w-full h-48 p-4 rounded-lg border bg-white dark:bg-gray-900
               dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600"
            placeholder="Write your SQL query here..."></textarea>

                <div class="mt-3">
                    <button
                        @click="runQuery()"
                        :disabled="!queryTemp"
                        class="px-3 py-1.5 rounded-md font-medium flex items-center gap-1 transition
                 shadow-sm cursor-pointer disabled:cursor-default
                 text-white bg-blue-600 dark:bg-blue-700
                 disabled:bg-gray-300 disabled:dark:bg-gray-700">
                        <i class="fa-solid fa-play"></i>
                        Run Query
                    </button>
                </div>
            </div>
        </div>
    </transition>

    <div v-if="tableType === 'data'" class="mt-5">
        <DataTable :key="tableKey" :columns="result.columns" :data="result.rows" :useQueryParams="false"/>
    </div>

    <div v-if="tableType === 'api' && url" class="mt-5">
        <DataTable :key="tableKey" :columns="result.columns" :url="url" :useQueryParams="false"/>
    </div>

    <div v-if="tableType === 'no-data' && query" class="mt-5 flex flex-col items-center text-center text-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            No data available
        </h3>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-sm leading-relaxed">
            It looks like no records are available to display for this table or query result.
            Please adjust your query and try again.
        </p>
    </div>
</template>

<script>
import {watch} from "vue";
import {useUiStore} from '../stores/UiStore';
import DataTable from "../components/DataTable.vue";
import {useRoute, useRouter} from "vue-router";
import {useToastStore} from '../stores/ToastStore';
import api from "../libraries/api.js";

export default {
    components: {DataTable},

    data() {
        return {
            collapsed: false,
            connection: null,
            loading: false,
            queryTemp: null,
            query: null,
            result: {
                columns: [],
                rows: [],
                total: 0,
            },
        };
    },

    mounted() {
        this.connection = this.route.params.connection;

        this.updateTitle();

        watch(
            () => this.route.params.connection,
            (newConn) => {
                if (!newConn) return;

                this.connection = newConn;

                this.updateTitle();
            }
        );
    },

    methods: {
        updateTitle() {
            document.title = "Database Manager - " + this.connection + " - Query";
        },

        async runQuery() {
            try {
                this.collapsed = false;
                this.query = this.queryTemp;

                this.result = {
                    columns: [],
                    rows: [],
                    total: 0,
                };

                this.ui.showLoading();

                const res = await api.get(
                    this.url,
                );

                Object.assign(this.result, res.data.result);

                this.updateColumns();
            } finally {
                this.collapsed = true;

                this.ui.hideLoading();
            }
        },

        updateColumns() {
            this.result.columns = this.result.columns.map(c => ({
                field: c,
                header: c,
                sortable: false,
            }));
        },
    },

    computed: {
        tableType() {
            if (this.result.total > 0 && this.result.total === this.result.rows.length) {
                return 'data';
            } else if (this.result.total > 0) {
                return 'api';
            } else {
                return 'no-data';
            }
        },

        url() {
            if (!this.query) return null;

            return '/' + DatabaseManager.basePath
                + '/api/' + this.connection
                + '/query?query=' + encodeURIComponent(this.query);
        },

        tableKey() {
            return JSON.stringify({
                columns: this.result.columns,
                connection: this.connection,
                url: this.url,
            });
        },
    },

    setup() {
        return {
            route: useRoute(),
            router: useRouter(),
            ui: useUiStore(),
            toast: useToastStore(),
        };
    },
}
</script>
