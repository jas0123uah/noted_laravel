<template>
    <WarningModal
    @eventSuccess="payload => res_message = payload"
    :key="modal.title"
    v-if="modal.title" 
    :modal_function="modal.modal_function"
    :warning_message="modal.message" 
    input_placeholder="Stack name..."
    :title="modal.title" />
    <div class="align-self-center">
        <scroll
            v-if="stacks"
            :is_stack="true"
            :items="stacks" >
        </scroll>
    </div>
    <p v-if="response_message" class="text-success m-2 text-center">{{ response_message }}</p>

</template>
<script>
import scroll from '../components/Scroll.vue';
import { useModalStore } from '@/stores/modal';
import { useStacksStore } from '@/stores/stacks';
import { useResponsemessageStore } from '@/stores/response_message';
export default {
    props: [],
    data() {
        return {
            modal_store: useModalStore(),
            stacks_store: useStacksStore(),
            response_message_store: useResponsemessageStore(),
        };
    },
    async created() {
        
        let stacks = (await window.axios.get('/stacks/')).data;
        this.stacks_store.setStacks(stacks.data);
    },
    computed: {
        modal(){
            return this.modal_store.getModal
        },
        stacks(){
            return this.stacks_store.getStacks
        },
        response_message(){
            return this.response_message_store.getResponseMessage
        }

    },
    methods: {

    },
};
</script>
