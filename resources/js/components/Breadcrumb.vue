<!--suppress JSUnresolvedReference -->
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

<script>
import {useRoute, useRouter} from 'vue-router';
import {watch} from "vue";

export default {
  data() {
    return {
      connection: null,
      table: null,
      items: [],
    };
  },

  mounted() {
    this.connection = this.route.params.connection;
    this.table = this.route.params.table;

    this.calcItems();

    watch(
        () => this.route.fullPath,
        () => {
          this.connection = this.route.params.connection;
          this.table = this.route.params.table;

          this.calcItems();
        }
    );
  },

  methods: {
    pageTitle() {
      return this.route.meta.title || this.route.name || '';
    },

    calcItems() {
      this.items = [];

      this.items.push({
        label: this.connection,
        to: {name: 'overview', params: {connection: this.connection}},
        clickable: true
      });

      if (this.table) {
        this.items.push({
          label: 'Tables',
          to: {name: 'tables', params: {connection: this.connection}},
          clickable: true
        });

        this.items.push({
          label: this.table,
          clickable: false
        });
      }

      this.items.push({
        label: this.pageTitle(),
        clickable: false
      });
    }
  },

  setup() {
    return {
      route: useRoute(),
      router: useRouter(),
    };
  }
}
</script>
