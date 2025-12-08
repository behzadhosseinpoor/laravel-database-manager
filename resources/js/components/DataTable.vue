<!--suppress JSUnusedGlobalSymbols -->
<template>
    <div class="space-y-4">
        <div class="overflow-x-auto rounded-md border border-gray-300 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700 text-center">
                <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.field"
                        @click="col.sortable && toggleSort(col.field)"
                        class="border-l border-gray-300 dark:border-gray-700 cursor-pointer select-none"
                        :class="[
                sizeClasses,
                col.sortable ? 'hover:bg-gray-200 dark:hover:bg-gray-700' : '',
                'text-center']">
                        <span>{{ col.header }}</span>
                        <span v-if="col.sortable" class="ml-1 text-xs">
              <span v-if="sortField === col.field && sortDir === 'asc'"><i class="fa-solid fa-arrow-up-short-wide"></i></span>
              <span v-else-if="sortField === col.field && sortDir === 'desc'"><i
                  class="fa-solid fa-arrow-down-wide-short"></i></span>
            </span>
                    </th>

                    <th v-if="showActions"
                        class="border-l border-gray-300 dark:border-gray-700 cursor-pointer select-none"
                        :class="[
                sizeClasses,
                'text-center']">
                        Actions
                    </th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-if="loading">
                    <td :colspan="columns.length + (showActions ? 1 : 0)"
                        class="py-6 text-gray-500 dark:text-gray-400 text-center">
                        Loading...
                    </td>
                </tr>

                <tr v-else-if="paginatedData.length === 0">
                    <td :colspan="columns.length + (showActions ? 1 : 0)"
                        class="py-6 text-gray-500 dark:text-gray-400 text-center">
                        No data available.
                    </td>
                </tr>

                <tr
                    v-else
                    v-for="row in paginatedData"
                    :key="row.id || row.name"
                    class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                    <td
                        v-for="col in columns"
                        :key="col.field"
                        :class="[
                            'border-l border-gray-300 dark:border-gray-700 align-middle',
                            'whitespace-nowrap overflow-hidden text-ellipsis max-w-[250px]',
                            sizeClasses]">
                        <template v-if="row[col.field] === null">
                            <span class="text-gray-400 dark:text-gray-500 italic">NULL</span>
                        </template>

                        <template v-else-if="isEncoded(row[col.field])">
                            <div class="flex items-center justify-center">
                                <BinaryDownload :base64="stripEncodedPrefix(row[col.field])"/>
                            </div>
                        </template>

                        <template v-else>
                            {{ col.format ? col.format(row[col.field]) : row[col.field] }}
                        </template>
                    </td>

                    <td v-if="showActions"
                        :class="['border-l border-gray-300 dark:border-gray-700', sizeClasses]">
                        <slot name="actions" :row="row"></slot>
                    </td>
                </tr>
                </tbody>

                <tfoot class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.field"
                        @click="col.sortable && toggleSort(col.field)"
                        class="border-l border-gray-300 dark:border-gray-700 cursor-pointer select-none"
                        :class="[
                sizeClasses,
                col.sortable ? 'hover:bg-gray-200 dark:hover:bg-gray-700' : '',
                'text-center']">
                        <span>{{ col.header }}</span>
                        <span v-if="col.sortable" class="ml-1 text-xs">
              <span v-if="sortField === col.field && sortDir === 'asc'"><i class="fa-solid fa-arrow-up-short-wide"></i></span>
              <span v-else-if="sortField === col.field && sortDir === 'desc'"><i
                  class="fa-solid fa-arrow-down-wide-short"></i></span>
            </span>
                    </th>

                    <th v-if="showActions"
                        class="border-l border-gray-300 dark:border-gray-700 cursor-pointer select-none"
                        :class="[
                sizeClasses,
                'text-center']">
                        Actions
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>


        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button
                    @click="currentPage--"
                    :disabled="currentPage <= 1 || totalPages === 0"
                    class="px-3 py-1 text-sm rounded-md border border-gray-300 dark:border-gray-700 disabled:opacity-30 cursor-pointer">
                    Previous
                </button>

                <span class="text-sm">
          Page {{ totalPages === 0 ? 0 : currentPage }} of {{ totalPages }}
        </span>

                <button
                    @click="currentPage++"
                    :disabled="currentPage >= totalPages || totalPages === 0"
                    class="px-3 py-1 text-sm rounded-md border border-gray-300 dark:border-gray-700 disabled:opacity-30 cursor-pointer">
                    Next
                </button>
            </div>

            <div class="flex items-center gap-2 text-sm">
                <span>View:</span>

                <select
                    v-model="pageSize"
                    class="border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 px-2 py-1">
                    <option v-for="n in pageSizes" :key="n" :value="n">
                        {{ n }}
                    </option>
                </select>

                <span>Records</span>
            </div>
        </div>
    </div>
</template>


<script>
import {useRoute, useRouter} from "vue-router"
import BinaryDownload from "./BinaryDownload.vue";
import api from "../libraries/api.js";

export default {
    components: {BinaryDownload},

    props: {
        data: Array,
        columns: Array,
        size: {type: String, default: "md"},
        showActions: {type: Boolean, default: false},
        defaultPageSize: {type: Number, default: 10},
        pageSizes: {type: Array, default: () => [5, 10, 20, 50, 100]},
        url: {type: String, default: null},
        useQueryParams: {type: Boolean, default: true}
    },

    setup() {
        return {route: useRoute(), router: useRouter()};
    },

    data() {
        return {
            currentPage: this.useQueryParams
                ? Number(this.route.query.page) || 1
                : 1,

            pageSize: this.useQueryParams
                ? Number(this.route.query.pageSize) || this.defaultPageSize
                : this.defaultPageSize,

            sortField: this.useQueryParams
                ? this.route.query.sort || null
                : null,

            sortDir: this.useQueryParams
                ? this.route.query.dir || null
                : null,

            serverData: [],
            serverTotal: 0,
            loading: false,
        }
    },

    methods: {
        toggleSort(field) {
            if (this.sortField !== field) {
                this.sortField = field;
                this.sortDir = "asc";

                return;
            }

            if (this.sortDir === "asc") {
                this.sortDir = "desc";
            } else if (this.sortDir === "desc") {
                this.sortField = null;
                this.sortDir = null;
            } else {
                this.sortDir = "asc";
            }
        },

        updateQueryParams() {
            if (!this.useQueryParams) return;

            this.router.replace({
                query: {
                    ...this.route.query,
                    page: this.currentPage,
                    pageSize: this.pageSize,
                    sort: this.sortField,
                    dir: this.sortDir
                }
            })
        },

        async loadServerData() {
            if (!this.url) return;

            this.loading = true;
            this.serverData = [];

            try {
                const res = await api.get(this.url, {
                    params: {
                        page: this.currentPage,
                        per_page: this.pageSize,
                        order_by: this.sortField,
                        order_type: this.sortDir
                    }
                });

                this.serverData = res.data.result.data ?? res.data.result.rows ?? [];
                this.serverTotal = res.data.result.total ?? 0;
            } finally {
                this.loading = false;
            }
        },

        isEncoded(value) {
            return typeof value === "string" && value.startsWith(":::___DATABASE___MANAGER___ENCODED___:::");
        },

        stripEncodedPrefix(value) {
            return value.replace(":::___DATABASE___MANAGER___ENCODED___:::", "");
        }
    },

    computed: {
        effectiveData() {
            return this.url ? this.serverData : this.data;
        },

        totalPages() {
            const total = this.url ? this.serverTotal : this.data.length;
            const pages = Math.ceil(total / this.pageSize);
            return pages > 0 ? pages : 1;
        },

        sizeClasses() {
            const sizes = {
                sm: "px-2 py-1 text-xs",
                md: "px-3 py-2 text-sm",
                lg: "px-4 py-3 text-base"
            }

            return sizes[this.size] || sizes.md;
        },

        sortedData() {
            if (this.url) return this.effectiveData;

            if (!this.sortField) return this.data;

            return [...this.data].sort((a, b) => {
                const fa = a[this.sortField];
                const fb = b[this.sortField];

                if (fa == null && fb == null) return 0;

                if (fa == null) {
                    return this.sortDir === "asc" ? -1 : 1;
                }

                if (fb == null) {
                    return this.sortDir === "asc" ? 1 : -1;
                }

                if (fa < fb) return this.sortDir === "asc" ? -1 : 1;
                if (fa > fb) return this.sortDir === "asc" ? 1 : -1;

                return 0;
            });
        },


        paginatedData() {
            if (this.url) return this.effectiveData;

            const start = (this.currentPage - 1) * this.pageSize;
            return this.sortedData.slice(start, start + this.pageSize);
        },

        tableState() {
            return [
                this.currentPage,
                this.pageSize,
                this.sortField,
                this.sortDir
            ];
        }
    },

    watch: {
        tableState() {
            if (this.useQueryParams) {
                this.updateQueryParams();
            }

            this.loadServerData();
        }
    },

    mounted() {
        if (this.url) {
            this.loadServerData();
        }
    }
}
</script>
