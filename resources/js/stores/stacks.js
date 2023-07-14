// stores/stacks.js
import { defineStore } from "pinia";
import _ from "lodash";
export const useStacksStore = defineStore("stacks", {
    state: () => ({
        stacks: [],
    }),
    getters: {
        getStacks() {
            return this.stacks;
        },
    },
    actions: {
        setStacks(stacks) {
            this.stacks = stacks.map((s) => {
                s.original = _.cloneDeep(s);
                return s;
            });
        },
        repsertStack(stack) {
            delete stack.original;
            stack.original = _.cloneDeep(stack);
            let i = this.stacks.findIndex(
                (s) => s.stack_id === stack.stack_id
            );
            if (i !== -1) {
                this.stacks[i] = stack;
            } else {
                this.stacks = [stack, ...this.stacks]; // Put at the start so the new s renders at the beginning of the list
            }
        },
        removeStack(stack) {
            let i = this.stacks.findIndex(
                (s) => s.stack_id === stack.stack_id
            );
            if (i !== -1) this.stacks.splice(i, 1);
        },
    },
});
