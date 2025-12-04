<template>
  <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
    <Sidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar"/>

    <div class="flex flex-col flex-1 h-full pl-1 transition-all duration-300">
      <Topbar @toggleSidebar="toggleSidebar"/>

      <div class="flex flex-col flex-1 pt-1 overflow-hidden">
        <div class="bg-white dark:bg-black rounded-md flex flex-col h-full overflow-hidden">
          <div
              class="shrink-0 rounded-t-md border-b-2 p-2 text-sm h-12 flex items-center justify-start border-b-gray-100 dark:border-b-gray-900">

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

export default {
  components: {Sidebar, Topbar},

  data() {
    return {
      sidebarCollapsed: false,
    }
  },

  methods: {
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
    }
  }
}
</script>