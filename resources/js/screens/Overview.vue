<!--suppress JSUnresolvedReference -->
<template>
  <div class="mb-5 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center
                bg-purple-500/10 text-purple-600 dark:text-purple-300
                dark:bg-purple-500/20 text-2xl">
        <i class="fa-solid fa-chart-pie"></i>
      </div>

      <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
          Overview
          <span class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                :title="'Connection: ' + connection">
            {{ connection }}
          </span>
        </h1>

        <p class="text-gray-500 dark:text-gray-400 mt-0.5">
          Driver: {{ overview.driver }}
        </p>
      </div>
    </div>
  </div>

  <div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div
          class="stat-card group">
        <div class="stat-label">Tables</div>
        <div class="stat-value">{{ overview.stats.table_count.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Columns</div>
        <div class="stat-value">{{ overview.stats.column_count.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Indexes</div>
        <div class="stat-value">{{ overview.stats.index_count.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Views</div>
        <div class="stat-value">{{ overview.stats.view_count.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Rows</div>
        <div class="stat-value">{{ overview.stats.total_rows.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Triggers</div>
        <div class="stat-value">{{ overview.stats.triggers.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Stored Procedures</div>
        <div class="stat-value">{{ overview.stats.procedures.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Functions</div>
        <div class="stat-value">{{ overview.stats.functions.toLocaleString() }}</div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Version</div>
        <div class="text-xl font-semibold">
          {{ overview.version }}
        </div>
      </div>

      <div class="stat-card group">
        <div class="stat-label">Database Size</div>
        <div class="text-xl font-semibold">
          {{ humanSize(overview.size) }}
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import {watch} from "vue";
import {useRoute, useRouter} from 'vue-router';
import {useUiStore} from '../stores/UiStore';

export default {
  components: {},

  data() {
    return {
      connection: null,
      overview: {
        driver: null,
        version: null,
        size: 0,
        stats: {
          table_count: 0,
          column_count: 0,
          view_count: 0,
          index_count: 0,
          primary_keys: 0,
          unique_indexes: 0,
          foreign_keys: 0,
          triggers: 0,
          procedures: 0,
          functions: 0,
          total_rows: 0,
          charset: null,
          collation: null,
          uptime_seconds: 0,
          active_connections: 0,
        }
      }
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
    loadOverview(conn) {
      return this.$http
          .get('/' + DatabaseManager.basePath + '/api/' + conn + '/overview')
          .then(res => {
            this.overview = res.data.result;
          });
    },

    updateTitle() {
      document.title = "Database Manager - " + this.connection + " - Overview";
    },

    getData() {
      this.ui.showLoading();
      this.loadOverview(this.connection).finally(() => this.ui.hideLoading());
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