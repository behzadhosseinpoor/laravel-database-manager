<template>
  <div class="space-y-4">
    <div class="overflow-x-auto rounded-md border border-gray-300 dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700 text-center">
        <thead class="bg-gray-100 dark:bg-gray-800">
        <tr>
          <th
              v-for="col in columns"
              :key="col.field"
              @click="col.sortable && toggleSort(col.field)"
              class="border-l border-gray-300 dark:border-gray-700 cursor-pointer select-none"
              :class="[
                sizeClasses,
                col.sortable ? 'hover:bg-gray-200 dark:hover:bg-gray-700' : '',
                'text-center']">
            <span>{{ col.header }}</span>
            <span v-if="col.sortable" class="ml-1 text-xs">
              <span v-if="sortField === col.field && sortDir === 'asc'"><i class="fa-solid fa-arrow-up-short-wide"></i></span>
              <span v-else-if="sortField === col.field && sortDir === 'desc'"><i
                  class="fa-solid fa-arrow-down-wide-short"></i></span>
            </span>
          </th>

          <th v-if="showActions"
              class="border-l border-gray-300 dark:border-gray-700 cursor-pointer select-none"
              :class="[
                sizeClasses,
                'text-center']">
            Actions
          </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <tr
            v-for="row in paginatedData"
            :key="row.id || row.name"
            class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
          <td
              v-for="col in columns"
              :key="col.field"
              :class="['border-l border-gray-300 dark:border-gray-700', sizeClasses]">
            {{ row[col.field] }}
          </td>

          <td v-if="showActions"
              :class="['border-l border-gray-300 dark:border-gray-700', sizeClasses]">
            <slot name="actions" :row="row"></slot>
          </td>
        </tr>
        </tbody>
      </table>
    </div>


    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-sm rounded-md border border-gray-300 dark:border-gray-700 disabled:opacity-30 cursor-pointer">
          Previous
        </button>

        <span class="text-sm">
          Page {{ currentPage }} of {{ totalPages }}
        </span>

        <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 text-sm rounded-md border border-gray-300 dark:border-gray-700 disabled:opacity-30 cursor-pointer">
          Next
        </button>
      </div>

      <div class="flex items-center gap-2 text-sm">
        <span>View:</span>

        <select
            v-model="pageSize"
            class="border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 px-2 py-1">
          <option v-for="n in pageSizes" :key="n" :value="n">
            {{ n }}
          </option>
        </select>

        <span>Records</span>
      </div>
    </div>
  </div>
</template>


<script>
import {useRoute, useRouter} from "vue-router"

export default {
  props: {
    data: Array,
    columns: Array,
    size: {type: String, default: "md"},
    showActions: {type: Boolean, default: false},
    defaultPageSize: {type: Number, default: 10},
    pageSizes: {type: Array, default: () => [5, 10, 20, 50, 100]}
  },

  setup() {
    return {route: useRoute(), router: useRouter()};
  },

  data() {
    return {
      currentPage: Number(this.route.query.page) || 1,
      pageSize: Number(this.route.query.pageSize) || this.defaultPageSize,

      sortField: this.route.query.sort || null,
      sortDir: this.route.query.dir || "asc"
    }
  },

  watch: {
    currentPage() {
      this.updateQueryParams();
    },
    pageSize() {
      this.currentPage = 1;
      this.updateQueryParams();
    },

    sortField() {
      this.updateQueryParams();
    },
    sortDir() {
      this.updateQueryParams();
    }
  },

  methods: {
    toggleSort(field) {
      if (this.sortField !== field) {
        this.sortField = field;
        this.sortDir = "asc";

        return;
      }

      if (this.sortDir === "asc") {
        this.sortDir = "desc";
      } else if (this.sortDir === "desc") {
        this.sortField = null;
        this.sortDir = null;
      } else {
        this.sortDir = "asc";
      }
    },

    updateQueryParams() {
      this.router.replace({
        query: {
          ...this.route.query,
          page: this.currentPage,
          pageSize: this.pageSize,
          sort: this.sortField,
          dir: this.sortDir
        }
      })
    }
  },

  computed: {
    sizeClasses() {
      const sizes = {
        sm: "px-2 py-1 text-xs",
        md: "px-3 py-2 text-sm",
        lg: "px-4 py-3 text-base"
      }

      return sizes[this.size] || sizes.md;
    },

    sortedData() {
      if (!this.sortField) return this.data;

      return [...this.data].sort((a, b) => {
        const fa = a[this.sortField];
        const fb = b[this.sortField];

        if (fa == null) return 1;
        if (fb == null) return -1;

        if (fa < fb) return this.sortDir === "asc" ? -1 : 1;
        if (fa > fb) return this.sortDir === "asc" ? 1 : -1;

        return 0;
      })
    },

    paginatedData() {
      const start = (this.currentPage - 1) * this.pageSize;

      return this.sortedData.slice(start, start + this.pageSize);
    },

    totalPages() {
      return Math.ceil(this.data.length / this.pageSize);
    }
  }
}
</script>
