<template>
  <header
      class="h-12 flex items-center justify-between pb-1 shrink-0">
    <nav class="flex items-center gap-2 text-sm font-medium">
      <RouterLink
          :to="{ name: 'overview', params: { connection } }"
          class="p-2 flex items-center gap-2 rounded-md
             hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400"
          :class="isActive('overview')
        ? 'text-blue-600 dark:text-blue-400 bg-gray-200 dark:bg-gray-800 shadow-sm'
        : 'text-gray-700 dark:text-gray-300'">
        <i class="fa-solid fa-chart-pie text-xs"></i>
        <span>Overview</span>
      </RouterLink>

      <RouterLink
          :to="{ name: 'tables', params: { connection } }"
          class="p-2 flex items-center gap-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400"
          :class="isActive('tables')
        ? 'text-blue-600 dark:text-blue-400 bg-gray-200 dark:bg-gray-800 shadow-sm'
        : 'text-gray-700 dark:text-gray-300'">
        <i class="fa-solid fa-table text-xs"></i>
        <span>Tables</span>
      </RouterLink>
    </nav>

    <div class="flex items-center gap-2">
      <button
          @click="cycleMode"
          class="w-9 h-9 rounded-md flex items-center justify-center cursor-pointer
           hover:bg-gray-300 dark:hover:bg-gray-700
           hover:text-blue-600 dark:hover:text-blue-400
           active:scale-95
           text-gray-700 dark:text-gray-200">
        <i v-if="mode === 'system'" class="fa-solid fa-desktop text-base"></i>
        <i v-else-if="mode === 'light'" class="fa-solid fa-sun text-base"></i>
        <i v-else class="fa-solid fa-moon text-base"></i>
      </button>
    </div>
  </header>
</template>

<script>
import {ref, watch} from 'vue';
import {useRoute} from 'vue-router';

export default {
  data() {
    return {
      connection: null,
    };
  },

  mounted() {
    this.connection = this.route.params.connection;

    const saved = localStorage.getItem('theme-mode');

    this.mode = saved ?? 'system';

    this.applyTheme(this.mode);

    watch(
        () => this.route.params.connection,
        (newConn) => {
          if (!newConn) return;

          this.connection = newConn;
        }
    );
  },

  methods: {
    cycleMode() {
      if (this.mode === 'system') this.mode = 'light';
      else if (this.mode === 'light') this.mode = 'dark';
      else this.mode = 'system';

      localStorage.setItem('theme-mode', this.mode);

      this.applyTheme(this.mode);
    },

    isActive(name) {
      return this.route.name === name;
    },

    applyTheme(val) {
      const root = document.getElementById('database-manager');

      if (val === 'light') {
        root.classList.remove('dark');
      } else if (val === 'dark') {
        root.classList.add('dark');
      } else {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) root.classList.add('dark');
        else root.classList.remove('dark');
      }
    }
  },

  setup() {
    return {
      route: useRoute(),
      mode: ref('system'),
    };
  }
}
</script>
