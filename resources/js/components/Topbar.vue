<template>
  <header
      class="h-12 flex items-center justify-end pb-1 shrink-0">
    <button
        @click="cycleMode"
        class="p-2 rounded flex items-center justify-center cursor-pointer">
      <i v-if="mode === 'system'" class="fa-solid fa-desktop"></i>

      <i v-else-if="mode === 'light'" class="fa-solid fa-sun"></i>

      <i v-else class="fa-solid fa-moon"></i>
    </button>
  </header>
</template>

<script setup>
import {onMounted, ref} from 'vue';

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

function applyTheme(val) {
  const root = document.getElementById('database-manager');

  if (val === 'light') {
    root.classList.remove('dark');
  } else if (val === 'dark') {
    root.classList.add('dark');
  } else if (val === 'system') {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (prefersDark) root.classList.add('dark');
    else root.classList.remove('dark');
  }
}
</script>
