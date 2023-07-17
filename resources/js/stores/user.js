// stores/user.js
import { defineStore } from "pinia";
import _ from "lodash";
export const useUserStore = defineStore("user", {
    state: () => ({
        user: {},
    }),
    getters: {
        getUser() {
            return this.user;
        },
    },
    actions: {
        setUser(user) {
            this.user = user;
        },
        hideConfirmEmail(){
            this.user.show_confirm_email = false

        }
    },
});
