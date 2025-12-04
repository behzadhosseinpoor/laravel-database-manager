<!--suppress JSUnresolvedReference -->
<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold tracking-tight">
        {{ connection }} Overview
      </h1>

      <span class="text-sm px-3 py-1.5 rounded-md
                   bg-blue-100 dark:bg-blue-900
                   text-blue-700 dark:text-blue-200
                   border border-blue-200/50 dark:border-blue-800/40">
        Driver: {{ overview.driver }}
      </span>
    </div>

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
import {computed, watch} from "vue";
import {useRoute} from 'vue-router';
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
    const route = useRoute();
    const ui = useUiStore();

    this.connection = computed(() => route.params.connection).value;

    document.title = "Database Manager - " + this.connection + " - Overview";

    ui.showLoading();
    this.loadOverview(this.connection).finally(() => ui.hideLoading());

    watch(
        () => route.params.connection,
        (newConn) => {
          if (!newConn) return;

          this.connection = newConn;
          document.title = "Database Manager - " + newConn + " - Overview";

          ui.showLoading();
          this.loadOverview(newConn).finally(() => ui.hideLoading());
        }
    );
  },

  methods: {
    loadOverview(conn) {
      return this.$http
          .get('/' + DatabaseManager.basePath + '/api/' + conn + '/overview')
          .then(res => {
            this.overview = res.data;
          });
    },

    humanSize(bytes) {
      const units = ['B', 'KB', 'MB', 'GB', 'TB'];
      let i = 0;
      while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
      }
      return bytes.toFixed(2) + ' ' + units[i];
    }
  },
}
</script>