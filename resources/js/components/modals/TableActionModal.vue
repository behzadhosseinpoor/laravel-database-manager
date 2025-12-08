<template>
    <div class="flex flex-col gap-6">

        <!-- Title -->
        <div class="flex items-center gap-3">
            <div
                class="h-10 w-10 flex items-center justify-center rounded-full
                       bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ messages[action].title }}
            </h2>
        </div>

        <!-- Description -->
        <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-sm">
            {{ messages[action].text(table) }}
        </p>

        <!-- Option -->
        <label
            class="flex items-center gap-3 select-none cursor-pointer text-gray-800 dark:text-gray-200">
            <input
                type="checkbox"
                v-model="enableForeignKeys"
                class="size-4 rounded border-gray-400 dark:border-gray-600 text-blue-600
                       focus:ring-blue-500 dark:bg-gray-800"/>

            <span class="text-sm">Enable foreign key checks</span>
        </label>

        <!-- Footer Actions -->
        <div class="flex justify-end gap-3 pt-4">
            <button
                @click="cancel"
                class="px-4 py-2 rounded-md text-sm font-medium
                       bg-gray-200 text-gray-800 cursor-pointer
                       dark:bg-gray-700 dark:text-gray-200
                       hover:bg-gray-300 dark:hover:bg-gray-600
                       transition">
                Cancel
            </button>

            <button
                @click="confirm"
                class="px-4 py-2 rounded-md text-sm font-medium
                       bg-red-600 text-white
                       hover:bg-red-700 cursor-pointer
                       transition shadow-sm">
                Execute
            </button>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        table: {type: String, required: true},
        action: {type: String, required: true},
    },

    emits: ['close'],

    data() {
        return {
            enableForeignKeys: true,

            messages: {
                truncate: {
                    title: "Truncate Table",
                    text: (t) =>
                        `Are you sure you want to run TRUNCATE on "${t}"? This will remove all rows permanently.`,
                },

                drop: {
                    title: "Drop Table",
                    text: (t) =>
                        `Are you sure you want to DROP the table "${t}"? This action is irreversible.`,
                },
            },
        };
    },

    methods: {
        confirm() {
            this.$emit("close", {
                confirmed: true,
                enableForeignKeys: this.enableForeignKeys,
            });
        },

        cancel() {
            this.$emit("close", {
                confirmed: false,
                enableForeignKeys: this.enableForeignKeys,
            });
        },
    },
};
</script>
