<template>
    <div class="fixed bottom-3 right-3 z-[2000] space-y-2 w-72">

        <div v-for="t in toasts" :key="t.id"
             :class="[
            'py-4 px-3 rounded-md shadow text-white flex justify-between items-center animate-fadein text-sm',
            t.type === 'success' ? 'bg-green-600' : '',
            t.type === 'error' ? 'bg-red-600' : '',
            t.type === 'warning' ? 'bg-yellow-600' : '',
            t.type === 'info' ? 'bg-blue-600' : '',
         ]">

            <span>{{ t.message }}</span>

            <button @click="remove(t.id)" class="ml-3 text-white/80 hover:text-white cursor-pointer">
                ×
            </button>
        </div>

    </div>
</template>

<script setup>
import {useToastStore} from '../stores/ToastStore';
import {storeToRefs} from 'pinia';

const toast = useToastStore();
const {toasts} = storeToRefs(toast);

const remove = id => toast.remove(id);
</script>
