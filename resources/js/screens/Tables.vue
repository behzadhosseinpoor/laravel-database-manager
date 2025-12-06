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

  <DataTable :data="tables" :columns="columns" :showActions="true">
    <template #actions="{ row }">
      <div class="flex items-center gap-2 justify-center">
        <button
            @click="browse(row)"
            class="px-3 py-1.5 rounded-md font-medium
             bg-blue-500 text-white hover:bg-blue-600
             dark:bg-blue-600 dark:hover:bg-blue-700
             flex items-center gap-1 transition cursor-pointer">
          <i class="fa-solid fa-table"></i>
          Browse
        </button>
      </div>
    </template>
  </DataTable>
</template>

<script>
import {computed, watch} from "vue";
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
    this.connection = computed(() => this.route.params.connection).value;

    document.title = "Database Manager - " + this.connection + " - Tables";

    this.ui.showLoading();
    this.loadTables(this.connection).finally(() => this.ui.hideLoading());

    watch(
        () => this.route.params.connection,
        (newConn) => {
          if (!newConn) return;

          this.connection = newConn;
          document.title = "Database Manager - " + this.connection + " - Tables";

          this.ui.showLoading();
          this.loadTables(this.connection).finally(() => this.ui.hideLoading());
        }
    );
  },

  methods: {
    loadTables(conn) {
      return this.$http
          .get('/' + DatabaseManager.basePath + '/api/' + conn + '/tables')
          .then(res => {
            this.tables = res.data;
          });
    },
    browse(row) {
      this.router.push({
        name: "table-browse",
        params: {table: row.name, connection: this.connection}
      })
    },
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