<!--suppress JSUnresolvedReference -->
<template>
  <div class="mb-5 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center
                bg-purple-500/10 text-purple-600 dark:text-purple-300
                dark:bg-purple-500/20 text-2xl">
        <i class="fa-solid fa-magnifying-glass"></i>
      </div>

      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
          Browse
          <span class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
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

  <DataTable
      v-if="connection && table"
      :key="tableKey"
      :columns="columns"
      :url="url">

  </DataTable>
</template>

<script>
import {watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import {useUiStore} from "../stores/UiStore.js";
import DataTable from "../components/DataTable.vue";

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
      columns: [],
    };
  },

  mounted() {
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
    loadStructure(conn, table) {
      return this.$http
          .get('/' + DatabaseManager.basePath + '/api/' + conn + '/tables/' + table + '/structure')
          .then(res => {
            this.structure = res.data;
            this.columns = this.mapColumnsForTable(this.structure);
          });
    },

    getData() {
      this.ui.showLoading();
      this.loadStructure(this.connection, this.table).finally(() => this.ui.hideLoading());
    },

    updateTitle() {
      document.title =
          "Database Manager - " +
          this.connection +
          " - " +
          this.table +
          " - Browse";
    },

    mapColumnsForTable(structureColumns) {
      return structureColumns.columns.map(c => ({
        field: c.name,
        header: c.name,
        sortable: true
      }));
    }
  },

  computed: {
    url() {
      return '/' + DatabaseManager.basePath + '/api/' + this.connection + '/tables/' + this.table + '/browse';
    },

    tableKey() {
      return JSON.stringify({
        columns: this.columns,
        connection: this.connection,
        table: this.table,
        url: this.url,
      });
    }
  },

  setup() {
    return {
      route: useRoute(),
      router: useRouter(),
      ui: useUiStore()
    };
  }
}
</script>