<template>
    <div class="d-flex flex-column">
        <span class="align-self-center">{{ stackTitle }}</span>
        <div class="d-flex flex-row gap-5 justify-content-center">
            <button
                :class="{ 'd-none': !stack_has_notecards }"
                class="btn button text-white border-0 p-2"
                @click="studyStack"
            >
                Study
            </button>
            <button
                :class="{ invisible: study_only }"
                class="btn button text-white border-0 p-2"
                @click="editStack"
            >
                Edit Stack
            </button>
        </div>
    </div>
</template>

<script>
import { useResponsemessageStore } from "@/stores/response_message";
import { useStacksStore } from "@/stores/stacks";
export default {
    props: {
        stackTitle: {
            type: String,
        },
        stackId: {
            required: true,
        },
        study_only: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            response_message_store: useResponsemessageStore(),
            stacks_store: useStacksStore(),
        };
    },
    created() {},
    computed: {
        stack() {
            return this.stacks_store.getStacks.find(
                (s) => s.stack_id === this.stackId
            );
        },
        stack_has_notecards() {
            return (
                this.study_only ||
                (this.stack &&
                    (this.stack?.notecards?.length > 1 ||
                        this?.stack?.notecards?.[0]?.notecard_id))
            );
        },
    },
    methods: {
        editStack() {
            this.$router.push(`/stacks/${this.stackId}/edit`);
            this.response_message_store.setResponseMessage({});
        },
        studyStack() {
            this.$router.push(`/stacks/${this.stackId}/study`);
            this.response_message_store.setResponseMessage({});
        },
    },
};
</script>
