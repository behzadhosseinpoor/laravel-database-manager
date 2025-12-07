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
          <span class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
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
        <button
            @click="browse(row)"
            class="px-3 py-1.5 rounded-md font-medium
           flex items-center gap-1 transition cursor-pointer
           shadow-sm hover:shadow-md
           text-white bg-blue-600 hover:bg-blue-700
           dark:bg-blue-700 dark:hover:bg-blue-800">
          <i class="fa-solid fa-magnifying-glass"></i>
          Browse
        </button>

        <button
            @click="structure(row)"
            class="px-3 py-1.5 rounded-md font-medium
           flex items-center gap-1 transition cursor-pointer
           shadow-sm hover:shadow-md
           text-white bg-red-600 hover:bg-red-700
           dark:bg-red-700 dark:hover:bg-red-800">
          <i class="fa-solid fa-diagram-project"></i>
          Structure
        </button>
      </div>
    </template>
  </DataTable>
</template>

<script>
import {watch} from "vue";
import {useUiStore} from '../stores/UiStore';
import DataTable from "../components/DataTable.vue";
import {useRoute, useRouter} from "vue-router";

export default {
  components: {DataTable},

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
    loadTables(conn) {
      return this.$http
          .get('/' + DatabaseManager.basePath + '/api/' + conn + '/tables')
          .then(res => {
            this.tables = res.data.result;
          });
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

    updateTitle() {
      document.title = "Database Manager - " + this.connection + " - Tables";
    },

    getData() {
      this.ui.showLoading();
      this.loadTables(this.connection).finally(() => this.ui.hideLoading());
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
        rows: this.tables.length,
      });
    }
  }
}
</script>