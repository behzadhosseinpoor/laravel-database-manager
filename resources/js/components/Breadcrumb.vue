<!--suppress JSUnresolvedReference -->
<script setup>
import {computed} from 'vue';
import {useRoute, useRouter} from 'vue-router';

const route = useRoute();
const router = useRouter();

const connection = computed(() => route.params.connection);

const table = computed(() => route.params.table);

const pageTitle = computed(() => {
  return route.meta.title || route.name || '';
});

const items = computed(() => {
  const list = [
    {
      label: connection.value,
      to: {name: 'overview', params: {connection: connection.value}},
      clickable: true
    }
  ];

  if (route.name === 'table') {
    list.push({
      label: 'Tables',
      to: {name: 'tables', params: {connection: connection.value}},
      clickable: true
    });
  }

  if (table.value) {
    list.push({
      label: table.value,
      clickable: false
    });
  } else {
    list.push({
      label: pageTitle.value,
      clickable: false
    });
  }

  return list;
});
</script>

<template>
  <nav class="text-gray-600 dark:text-gray-400 flex items-center h-full gap-1">
    <template v-for="(item, index) in items" :key="index">
      <span
          v-if="item.clickable"
          class="cursor-pointer hover:underline"
          @click="router.push(item.to)"
      >
        {{ item.label }}
      </span>

      <span v-else>
        {{ item.label }}
      </span>

      <span v-if="index < items.length - 1" class="mx-1">/</span>
    </template>
  </nav>
</template>
