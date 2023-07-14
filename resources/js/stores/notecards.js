// stores/notecards.js
import { defineStore } from "pinia";
import _ from "lodash";
export const useNotecardsStore = defineStore("notecards", {
    state: () => ({
        notecards: [],
    }),
    getters: {
        getNotecards() {
            return this.notecards;
        },
    },
    actions: {
        setNotecards(notecards) {
            this.notecards = notecards.map(nc =>{
                nc.original = _.cloneDeep(nc);
                return nc;
            });
        },
        repsertNotecard(notecard){
            delete notecard.original;
            notecard.original = _.cloneDeep(notecard)
            let i = this.notecards.findIndex(nc => nc.notecard_id === notecard.notecard_id);
            console.log(notecard, "REPSERTING THIS")
            console.log(this.notecards, "NCS")
            console.log(i, "IIII");
            if (i !== -1) {
                this.notecards[i] = notecard
            } else {
                this.notecards = [notecard, ...this.notecards] // Put at the start so the new nc renders at the beginning of the list
            }
        },
        removeNotecard(notecard){
            let i = this.notecards.findIndex(nc => nc.notecard_id === notecard.notecard_id);
            if (i !== -1) this.notecards.splice(i, 1);
        }
    },
});
