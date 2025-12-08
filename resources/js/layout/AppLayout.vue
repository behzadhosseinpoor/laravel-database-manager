<!--suppress JSUnresolvedReference -->
<template>
    <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
        <GlobalToast/>

        <ModalContainer/>

        <Sidebar/>

        <div class="flex flex-col flex-1 h-full pl-1">
            <Topbar/>

            <div class="flex flex-col flex-1 pt-1 overflow-hidden w-[calc(100vw-4rem)]">
                <div class="bg-white dark:bg-black rounded-md flex flex-col h-full overflow-hidden">
                    <div
                        class="shrink-0 border-b-2 p-4 text-sm h-12 border-b-gray-100 dark:border-b-gray-900">
                        <Breadcrumb/>
                    </div>

                    <main class="flex-1 overflow-auto overscroll-contain relative">
                        <div class="p-4 min-h-full">
                            <transition name="fade">
                                <div
                                    v-if="!ready"
                                    class="absolute inset-0 flex items-center justify-center bg-white/95 dark:bg-black/95 z-10">
                                    <div
                                        class="animate-spin h-10 w-10 border-2 border-gray-300 border-t-purple-400 rounded-full"></div>
                                </div>
                            </transition>

                            <router-view/>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue'
import Topbar from '../components/Topbar.vue'
import {useConnectionStore} from '../stores/ConnectionStore';
import {useRoute, useRouter} from 'vue-router';
import Breadcrumb from "../components/Breadcrumb.vue";
import {useUiStore} from '../stores/UiStore';
import GlobalToast from "../components/GlobalToast.vue";
import {useToastStore} from '../stores/ToastStore';
import ModalContainer from '../components/ModalContainer.vue';

export default {
    components: {ModalContainer, GlobalToast, Breadcrumb, Sidebar, Topbar},

    computed: {
        ready() {
            return this.ui.ready;
        }
    },

    mounted() {
        this.store.setConnections(JSON.parse(window.DatabaseManager.connections));
        this.store.setDefault(window.DatabaseManager.default_connection);

        if (this.route.params.connection && !this.store.connections.includes(this.route.params.connection)) {
            this.toast.show('[' + this.route.params.connection + '] must exist in connections.', 'error');

            this.router.push({
                name: 'overview',
                params: {connection: this.store.defaultConnection}
            });
        }
    },

    setup() {
        return {
            store: useConnectionStore(),
            route: useRoute(),
            router: useRouter(),
            toast: useToastStore(),
            ui: useUiStore(),
        };
    }
}
</script>
