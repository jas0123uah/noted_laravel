// stores/selected_notecard.js
import { defineStore } from "pinia";

import _ from "lodash";
import { useResponsemessageStore } from "./response_message";

export const useSelectednotecardStore = defineStore("selected_notecard", {
    state: () => ({
        selected_notecard: null
    }),

    getters: {
        getSelectedNotecard(){
            return this.selected_notecard;
        }

    },
    actions: {
        setSelectedNotecard(selected_notecard){
            const response_message_store = useResponsemessageStore();
            //Remove any previous in-line response message from the screen, e.g., Stack created successfully
            response_message_store.response_message = null;
            delete selected_notecard.original;
            selected_notecard.original = _.cloneDeep(selected_notecard);
            this.selected_notecard = selected_notecard;
        }
    },
});
