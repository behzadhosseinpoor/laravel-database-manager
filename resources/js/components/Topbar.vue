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

<script setup>
import {onMounted, ref} from 'vue';
import {useRoute} from 'vue-router';

const route = useRoute();

const connection = route.params.connection;

const mode = ref('system');

onMounted(() => {
  const saved = localStorage.getItem('theme-mode');
  mode.value = saved ?? 'system';
  applyTheme(mode.value);
})

function cycleMode() {
  if (mode.value === 'system') mode.value = 'light';
  else if (mode.value === 'light') mode.value = 'dark';
  else mode.value = 'system';

  localStorage.setItem('theme-mode', mode.value);
  applyTheme(mode.value);
}

function isActive(name) {
  return route.name === name;
}

function applyTheme(val) {
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
</script>
