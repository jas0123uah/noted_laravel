// stores/modal.js
import { defineStore } from "pinia";
import _ from "lodash";
export const useModalStore = defineStore("modal", {
    state: () => ({
        modal: {},
    }),
    getters: {
        getModal() {
            return this.modal;
        },
    },
    actions: {
        setModal(modal) {
            this.modal = modal;
        },
    },
});
