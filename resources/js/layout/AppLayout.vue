<!--suppress JSUnresolvedReference -->
<template>
  <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
    <Sidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar"/>

    <div class="flex flex-col flex-1 h-full pl-1 transition-all duration-300">
      <Topbar @toggleSidebar="toggleSidebar"/>

      <div class="flex flex-col flex-1 pt-1 overflow-hidden">
        <div class="bg-white dark:bg-black rounded-md flex flex-col h-full overflow-hidden">
          <div
              class="shrink-0 rounded-t-md border-b-2 p-2 text-sm h-12 border-b-gray-100 dark:border-b-gray-900">
            <Breadcrumb/>
          </div>

          <main class="flex-1 overflow-y-auto overscroll-contain rounded-b-md p-2">
            <router-view/>
          </main>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import Topbar from '../components/Topbar.vue'
import {useConnectionStore} from '../stores/ConnectionStore';
import {useRoute, useRouter} from 'vue-router';
import Breadcrumb from "../components/Breadcrumb.vue";

export default {
  components: {Breadcrumb, Sidebar, Topbar},

  data() {
    return {
      sidebarCollapsed: true,
    }
  },

  methods: {
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
    }
  },

  mounted() {
    const store = useConnectionStore();
    const route = useRoute();
    const router = useRouter();

    store.setConnections(JSON.parse(window.DatabaseManager.connections));
    store.setDefault(window.DatabaseManager.default_connection);

    if (route.params.connection) {
      store.setDefault(route.params.connection);
    }

    if (!route.params.connection) {
      router.push({
        name: 'dashboard',
        params: {connection: store.defaultConnection}
      });
    }

    this.$watch(
        () => this.$route.params.connection,
        (val) => {
          if (val) {
            store.setDefault(val);
          }
        }
    );
  }
}
</script>
