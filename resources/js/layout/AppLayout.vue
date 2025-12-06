<!--suppress JSUnresolvedReference -->
<template>
  <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
    <GlobalToast/>

    <Sidebar/>

    <div class="flex flex-col flex-1 h-full pl-1">
      <Topbar/>

      <div class="flex flex-col flex-1 pt-1 overflow-hidden">
        <div class="bg-white dark:bg-black rounded-md flex flex-col h-full overflow-hidden">
          <div
              class="shrink-0 border-b-2 p-4 text-sm h-12 border-b-gray-100 dark:border-b-gray-900">
            <Breadcrumb/>
          </div>

          <main class="flex-1 overflow-y-auto overscroll-contain relative">
            <div class="p-4 min-h-full">
              <transition name="fade">
                <div
                    v-if="!ready"
                    class="absolute inset-0 flex items-center justify-center bg-white/95 dark:bg-black/95 z-10">
                  <div class="animate-spin h-10 w-10 border-2 border-gray-300 border-t-purple-400 rounded-full"></div>
                </div>
              </transition>

              <router-view/>
            </div>
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
import {useUiStore} from '../stores/UiStore';
import GlobalToast from "../components/GlobalToast.vue";
import {useToastStore} from '../stores/ToastStore'

export default {
  components: {GlobalToast, Breadcrumb, Sidebar, Topbar},

  computed: {
    ready() {
      const ui = useUiStore();
      return ui.ready;
    }
  },

  mounted() {
    const store = useConnectionStore();
    const route = useRoute();
    const router = useRouter();
    const toast = useToastStore();

    store.setConnections(JSON.parse(window.DatabaseManager.connections));
    store.setDefault(window.DatabaseManager.default_connection);

    if (route.params.connection && !store.connections.includes(route.params.connection)) {
      toast.show('[' + route.params.connection + '] must exist in connections.', 'error');

      router.push({
        name: 'overview',
        params: {connection: store.defaultConnection}
      });
    }

    if (route.params.connection) {
      store.setDefault(route.params.connection);
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
