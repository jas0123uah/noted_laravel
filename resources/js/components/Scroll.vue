<template>
    <WarningModal
    :key="modal.message"
    v-if="modal.message" 
    :modal_function="modal.modal_function"
    :warning_message="modal.message" 
    :title="modal.type" />
    <div style="max-width: 30em; overflow-x: scroll; white-space: nowrap;" class="d-flex gap-5 align-items-center" ref="scrollContainer">
        <i @click="addNotecard();" class="fa-solid fa-2xl fa-circle-plus"></i>
        <!-- <span>{{ Object.keys(items).length  }}</span> -->
        <template v-if="is_stack">
            <homepage-notecard v-for="(item, stack_title) in items" :stack_title="stack_title" style="flex-shrink: 0;" @click="selectNotecard(item);" :item="item"></homepage-notecard>
        </template>
        <template v-else>
            <homepage-notecard v-for="(item) in items"  style="flex-shrink: 0;" @click="selectNotecard(item);" :item="item"></homepage-notecard>

        </template>
    </div>


</template>
<script>
import _ from 'lodash';
import { useSelectednotecardStore } from '@/stores/selected_notecard'
import { useModalStore } from '@/stores/modal'
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
    //props: ['items'],
    data() {
        return {
            selected_notecard_store: useSelectednotecardStore(),
            modal_store: useModalStore(),
            warning_message: null,
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

        }

    },
};
</script>
