import {defineStore} from 'pinia';

export const useModalStore = defineStore('modal', {
    state: () => ({
        show: false,
        component: null,
        props: {},
        resolve: null,
        closable: true,
        size: "md",
    }),

    actions: {
        open(component, props = {}, options = {}) {
            return new Promise((resolve) => {
                this.show = true;

                this.component = component;
                this.props = props;
                this.resolve = resolve;

                this.size = options.size || "md";
                this.closable = options.closable ?? true;
            })
        },

        close(result) {
            this.show = false;

            this.resolve?.(result ?? null);
        },

        cancel() {
            this.show = false;

            this.resolve?.(null);
        },
    }
});
