<template>
  <header
      class="h-12 flex items-center justify-between pb-1 shrink-0">
    <nav class="flex items-center gap-2 text-sm font-medium">
      <button
          v-for="item in menuItems"
          :key="item.label"
          @click="item.click"
          :class="[
            'p-2 flex items-center gap-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer',
            item.active ? 'text-blue-600 dark:text-blue-400 bg-gray-200 dark:bg-gray-800 shadow-sm' : 'text-gray-700 dark:text-gray-300'
        ]">
        <i :class="['fa-solid', `fa-${item.icon}`, 'text-xs']"></i>
        <span>{{ item.label }}</span>
      </button>
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
import {watch} from 'vue';
import {useRoute, useRouter} from 'vue-router';

export default {
  data() {
    return {
      connection: null,
      table: null,
      mode: 'system',
    };
  },

  mounted() {
    this.connection = this.route.params.connection;
    this.table = this.route.params.table;

    const saved = localStorage.getItem('theme-mode');

    this.mode = saved ?? 'system';

    this.applyTheme(this.mode);

    watch(
        () => this.route.params.connection,
        (newConn) => {
          this.connection = newConn || null;
        }
    );

    watch(
        () => this.route.params.table,
        (newTable) => {
          this.table = newTable || null;
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
      router: useRouter(),
    };
  },

  computed: {
    menuItems() {
      const items = [
        {
          label: 'Overview',
          icon: 'chart-pie',
          active: this.route.name === 'overview',
          click: () => this.router.push({name: 'overview', params: {connection: this.connection.value}})
        },
        {
          label: 'Tables',
          icon: 'table',
          active: this.route.name === 'tables',
          click: () => this.router.push({name: 'tables', params: {connection: this.connection.value}})
        }
      ];

      if (this.table) {
        items.push({
          label: `Browse`,
          icon: 'magnifying-glass',
          active: this.route.name === 'table-browse',
          click: () => this.router.push({
            name: 'table-browse',
            params: {connection: this.connection.value, table: this.table.value}
          })
        });
      }

      return items;
    }
  }
}
</script>
