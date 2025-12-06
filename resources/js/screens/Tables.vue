<!--suppress JSUnresolvedReference -->
<template>
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

        <button
            @click="structure(row)"
            class="px-3 py-1.5 rounded-md text-sm font-medium
             bg-gray-600 text-white hover:bg-gray-700
             dark:bg-gray-700 dark:hover:bg-gray-800
             flex items-center gap-1 transition cursor-pointer">
          <i class="fa-solid fa-diagram-project"></i>
          Structure
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
      tables: [],
      columns: [
        {field: 'name', header: 'Name', sortable: true},
        {field: 'rows', header: 'Rows', sortable: true},
        {field: 'engine', header: 'Engine', sortable: true},
        {field: 'collation', header: 'Collation', sortable: true},
        {field: 'size', header: 'Size', sortable: true},
      ]
    };
  },

  mounted() {
    const conn = computed(() => this.route.params.connection).value;

    document.title = "Database Manager - " + conn + " - Tables";

    this.ui.showLoading();
    this.loadTables(conn).finally(() => this.ui.hideLoading());

    watch(
        () => this.route.params.connection,
        (newConn) => {
          if (!newConn) return;

          document.title = "Database Manager - " + newConn + " - Tables";

          this.ui.showLoading();
          this.loadTables(newConn).finally(() => this.ui.hideLoading());
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

    structure(row) {
      this.router.push({
        name: "table-structure",
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