<template>
    <Teleport to="body">
        <transition name="fade">
            <div v-if="modal.show"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50"
                 @click="handleBackdrop">

                <transition name="scale">
                    <div
                        class="rounded-md shadow-lg
                       bg-white dark:bg-gray-900
                       text-gray-800 dark:text-gray-200
                       border border-gray-300 dark:border-gray-700
                       overflow-hidden flex flex-col p-3"
                        :class="sizeClass"
                        @click.stop>

                        <component
                            :is="modal.component"
                            v-bind="modal.props"
                            @close="modal.close"
                        />
                    </div>
                </transition>

            </div>
        </transition>
    </Teleport>
</template>

<script>
import {useModalStore} from "../stores/ModalStore.js";

export default {
    computed: {
        sizeClass() {
            switch (this.modal.size) {
                case 'sm':
                    return 'w-[360px]';
                case 'lg':
                    return 'w-[640px]';
                case 'xl':
                    return 'w-[800px]';
                default:
                    return 'w-[480px]';
            }
        }
    },

    created() {
        window.addEventListener("keydown", this.onKey);
    },

    unmounted() {
        window.removeEventListener("keydown", this.onKey);
    },

    methods: {
        onKey(e) {
            if (e.key === "Escape" && this.modal.closable) {
                this.modal.cancel();
            }
        },

        handleBackdrop() {
            if (this.modal.closable) {
                this.modal.cancel();
            }
        },
    },

    setup() {
        return {
            modal: useModalStore(),
        };
    }
}
</script>
