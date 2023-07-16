<template>
    <WarningModal
    :key="modal.message"
    v-if="modal.message || warning_message" 
    :modal_function="modal.modal_function"
    :warning_message="modal.message || warning_message" 
    :title="modal.type || 'WARNING'" />
    <div name="scroll" style="max-width: 30em; overflow-x: scroll; white-space: nowrap;"  class="d-flex align-items-center" :class="{'flex-column':!items.length, 'gap-3': !items.length, 'gap-5': items.length}" ref="scrollContainer">
        <span v-if="!items.length"> No stacks. Create one!</span>
        <i style="line-height: normal;" @click="is_stack ? addStackModal() : addNotecard();" class="fa-solid fa-2xl fa-circle-plus"></i>
        <template v-if="is_stack">
            <homepage-notecard v-for="(item) in items" :stack_title="item.name" style="flex-shrink: 0;" @click="selectNotecard(item);" :item="item"></homepage-notecard>
        </template>
        <template v-else>
            <homepage-notecard v-for="(item) in items"  style="flex-shrink: 0;" @click="selectNotecard(item);" :item="item"></homepage-notecard>
        </template>
    </div>
</template>
<script>
import _ from 'lodash';
import { useSelectednotecardStore } from '@/stores/selected_notecard'
import { useResponsemessageStore } from '@/stores/response_message'
import { useModalStore } from '@/stores/modal'
import { useStacksStore } from '@/stores/stacks'
import { storeToRefs } from 'pinia'
import WarningModal from '../components/WarningModal.vue';
import HomepageNotecard from './HomepageNotecard.vue';
export default {
    props: {
        items: {
            type: Array,
            required: true
        },
        is_stack : {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            selected_notecard_store: useSelectednotecardStore(),
            response_message_store: useResponsemessageStore(),
            modal_store: useModalStore(),
            stack_store: useStacksStore(),
            warning_message: null,
            new_stack_name: ''
        };
    },
    created() {
    },
    computed: {
        unsaved_changes() {
            if(this.selected_notecard_store){

                let selected_notecard = this.selected_notecard_store.getSelectedNotecard;
                return selected_notecard && !_.isEqual(_.omit(selected_notecard, 'original'), _.cloneDeep(selected_notecard.original));
            }
        },
        modal(){
            return this.modal_store.getModal
        },
        stacks(){
            return this.stack_store.getStacks
        },
        response_message(){
            return this.response_message_store.getResponseMessage
        },
        
    },
    methods: {
        addNotecard(){
            if(this.unsaved_changes){
                this.warning_message = "You have unsaved notecard changes. Please save before proceeding.";
                return
            }
            this.selected_notecard_store.setSelectedNotecard(
                {
                    front: "",
                    back: "",
                    original: {
                        front: "",
                        back: "",
                    }
                }
            );
        },
        selectNotecard(notecard){
            if(this.unsaved_changes){
                this.warning_message = "You have unsaved notecard changes. Please save before proceeding.";
                return
            }
            this.selected_notecard_store.setSelectedNotecard(notecard);

        },
        addStackModal(){
            this.modal_store.setModal({
                title: 'Create Stack',
                modal_function: async(stack_name) => {await this.addStackAPI(stack_name)},
            })
        },
        async addStackAPI(stack_name){
            try {
                let res = await window.axios.post('/stacks', {name: stack_name});
                this.stack_store.repsertStack(res.data.data);
                this.response_message_store.setResponseMessage(res.data.message);              
            } catch (error) {
                console.error(error);
                if (error.response.status > 400) {
                    this.modal_store.setModal({
                        title: 'ERROR',
                        message:`The following errors occurred: 
                        <ul>
                        ${Object.values(error.response.data.errors).map(e => `<li>${e}</li>`).join('')}   
                        </ul>
                    `
                    });
                
                }
            }
        }
    },
};
</script>
