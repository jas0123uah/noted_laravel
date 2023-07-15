// stores/response_message.js
import { defineStore } from "pinia";
import _ from "lodash";
export const useResponsemessageStore = defineStore("response_message", {
    state: () => ({
        response_message: null,
    }),

    getters: {
        getResponseMessage() {
            return this.response_message;
        },
    },
    actions: {
        setResponseMessage(response_message) {
            this.response_message = response_message;
        },
        formatResponseMessage(response_message) {
            return `The following errors occurred: 
                        <ul>
                        ${Object.values(response_message.errors)
                            .map((e) => `<li>${e}</li>`)
                            .join("")}   
                        </ul>
                    `;
        },
    },
});
