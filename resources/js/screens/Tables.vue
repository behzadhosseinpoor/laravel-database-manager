<!--suppress JSUnresolvedReference -->
<template>
</template>

<script>
import {computed, watch} from "vue";
import {useRoute} from 'vue-router';
import {useUiStore} from '../stores/UiStore';

export default {
  components: {},

  data() {
    return {
      tables: [],
    };
  },

  mounted() {
    const route = useRoute();
    const ui = useUiStore();

    const conn = computed(() => route.params.connection).value;

    document.title = "Database Manager - " + conn + " - Tables";

    ui.showLoading();
    this.loadTables(conn).finally(() => ui.hideLoading());

    watch(
        () => route.params.connection,
        (newConn) => {
          if (!newConn) return;

          document.title = "Database Manager - " + newConn + " - Tables";

          ui.showLoading();
          this.loadTables(newConn).finally(() => ui.hideLoading());
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
    }
  },
}
</script>