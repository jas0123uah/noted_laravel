<template>
    <div class="d-flex flex-column">
        <div :key="item.id" @click="selected_notecard_store.setSelectedNotecard(item)" class="card bg-white" style="width: 18rem; height: 9rem; display: flex; position: static; align-items: center;">
            <i v-if="can_be_deleted" @click="confirmDelete(item)" class="fa-solid fa-circle-minus hover-pointer" style="margin-top: 0.25rem; margin-right: 0.5rem; align-self: baseline;"></i>
            <div class=" justify-content-center card-body d-flex align-items-center" style="margin-bottom: 14.4px;">
                <h5 v-html="notecard_text" class="card-title roboto fw-bold"></h5>
            </div>
        </div>
        <stack-metadata v-if="stack_title" class="align-self-center" :stack-id="item.stack_id" :stack-title="stack_title"></stack-metadata>
    </div>
    
</template>

<script>
import _ from 'lodash';
import { useModalStore } from '@/stores/modal'
import { useNotecardsStore } from '@/stores/notecards'
import { useStacksStore } from '@/stores/stacks'
import { useResponsemessageStore } from '@/stores/response_message'
import { useSelectednotecardStore } from '@/stores/selected_notecard'
import { storeToRefs } from 'pinia'
import hljs from 'highlight.js'
import 'highlight.js/styles/default.css'; // Import the desired code highlighting style
import 'highlight.js/styles/monokai-sublime.css'

hljs.configure({   // optionally configure hljs
    languages: ['javascript', 'ruby', 'python']
});
export default {
    props: {
        item: {
            type: Object,
            required: true
        },
        can_be_deleted: {
            type: Boolean,
            default: true
        },
        stack_title: {
            type: String,
            default: ''
        },
        allow_truncate: {
            type: Boolean,
            default: true
        }
    },
    data(){
        return {
            modal_store: useModalStore(),
            notecards_store: useNotecardsStore(),
            stacks_store: useStacksStore(),
            response_message_store: useResponsemessageStore(),
            selected_notecard_store: useSelectednotecardStore(),

        }
    },
    computed:{
        modal(){
            return this.modal_store.getModal;
        },
        selected_notecard(){
            return this.selected_notecard_store.getSelectedNotecard;
        },
        stacks(){
            return this.stacks_store.getStacks;
        },
        notecard_text(){
            /*We use this component to represet both a single notecard as well as the first notecard in a stack on our home page
            The text we display should be either:
                1.The text from the passed in item if it's a notecard
                2. The text from the first notecard in the stack
                3. Text that reads Empty stack 
             */ 
            let string = 
                this.item?.original?.front || //The text on the front of the notecard before editing 
                this.item?.front ||  //The text on the front of the notecard in cases where the notecard is not selected
                this.stacks.find(s => s.stack_id == this.item.stack_id)?.notecards?.[0]?.front || //The first notecard in the stack
                "<p>Stack is empty</p>"
            if(this.allow_truncate) string =_.truncate(string, {length: 20});
            if(!string.endsWith("</p>")) string+="</p>"
            return string;
            

        }
    },
    methods: {
        confirmDelete(item){
            //Item may be a stack or notecard
            this.modal_store.setModal({
                type: 'CONFIRM',
                message: `<span>Are you sure you want to delete:</span> <p class="mx-1 ignore-subseq-p">${_.truncate(item.front || item.name)}</p> <span>This action cannot be undone.</span>`,
                modal_function: async () => {await this.deleteItem(item)}
            })
        },
        async deleteItem(item) {
            //Item may be a stack or notecard
            let item_type = this.stack_title ? 'stack' : 'notecard'
            try {
                let res = await window.axios.delete(`/api/${item_type}s/${item[`${item_type}_id`]}`);
                this.response_message_store.setResponseMessage(res.data.message);
                if(item_type === 'notecard') {
                    this.notecards_store.removeNotecard(item);
                    if(item.notecard_id === this.selected_notecard.notecard_id){
                        //Unselect the notecard they just deleted
                        this.selected_notecard_store.setSelectedNotecard({})
                    }
                }
                else this.stacks_store.removeStack(item);
            } catch (error) {
                console.error(error)
                if (error.response.status >400) {
                    this.modal_store.setModal({
                        type: 'ERROR',
                        message:`The following errors occurred: 
                        <ul>
                        ${Object.values(error.response.data.errors).map(e => `<li>${e}</li>`).join('')}   
                        </ul>
                    `
                    });
                
                }
                
            }
        }

        
    }
}
</script>
