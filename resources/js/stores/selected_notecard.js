// stores/selected_notecard.js
import { defineStore } from "pinia";
import _ from "lodash";
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
            delete selected_notecard.original;
            selected_notecard.original = _.cloneDeep(selected_notecard);
            this.selected_notecard = selected_notecard;
        }
    },
});
