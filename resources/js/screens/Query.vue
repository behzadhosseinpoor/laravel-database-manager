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
          <span class="text-sm px-2 py-1 rounded-md bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
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
            v-model="query"
            class="w-full h-48 p-4 rounded-lg border bg-white dark:bg-gray-900
               dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600"
            placeholder="Write your SQL query here..."></textarea>

        <div class="mt-3">
          <button
              @click="runQuery()"
              :disabled="!query"
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

  <div v-if="tableType === 'api'" class="mt-5">
    <DataTable :key="tableKey" :columns="result.columns" :url="url" :useQueryParams="false"/>
  </div>
</template>

<script>
import {watch} from "vue";
import {useUiStore} from '../stores/UiStore';
import DataTable from "../components/DataTable.vue";
import {useRoute, useRouter} from "vue-router";
import {useToastStore} from '../stores/ToastStore';

export default {
  components: {DataTable},

  data() {
    return {
      collapsed: false,
      connection: null,
      loading: false,
      query: null,
      result: {
        ok: null,
        type: null,
        columns: [],
        rows: [],
        total: null,
        affected: null,
        page: null,
        perPage: null,
        message: null,
        code: null,
        time: null,
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
      this.collapsed = false;
      this.ui.showLoading();

      try {
        const res = await this.$http.get(
            this.url
        );

        Object.assign(this.result, res.data.result);

        this.updateColumns();
      } catch (e) {
        this.result.ok = false;
        this.result.type = 'error';
        this.result.message = e.response?.data?.result?.message || e.message;
      } finally {
        this.ui.hideLoading();

        if (this.result.type === 'error' && !this.result.ol) {
          this.toast.show(this.result.message, 'error');
        } else if (this.result.type === 'write' && this.result.ok) {
          this.toast.show(this.result.message + '— affected rows: ' + this.result.affected);
        }

        if (this.result.type === 'select') {
          this.collapsed = true;
        }
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
      if (this.result.type === 'select' && this.result.total === this.result.rows.length) {
        return 'data';
      } else if (this.result.type === 'select') {
        return 'api';
      }

      return 'unknown';
    },

    url() {
      return '/' + DatabaseManager.basePath + '/api/' + this.connection + '/query?query=' + this.query;
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