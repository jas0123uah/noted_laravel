<template>
  
    <div class=" d-flex flex-column gap-5 gap-md-3 align-items-center">
        <!-- {{ stack }} -->
        <input type="text" v-if="stack" v-model="stack.name" @input="debounceEditStackName" placeholder="Stack name..." class="form-control input-group">
        <div class="align-self-center">
          <scroll
          v-if="stack?.notecards?.length"
          :items="notecards" >
        </scroll>
        </div>
      <div v-if="!stack?.notecards.length">
        <h5 class="mb-3 text-center">No notecards. Create some!</h5>
        <div class="mb-3">
          <edit-selected-notecard :add_padding="!!stack?.notecards.length" :key="selected_notecard.notecard_id" v-if="selected_notecard" :notecard="selected_notecard"></edit-selected-notecard>
        </div>
      </div>
      <edit-selected-notecard v-else :add_padding="!!stack.notecards.length"   :key="selected_notecard.notecard_id" v-if="selected_notecard" :notecard="selected_notecard"></edit-selected-notecard>
      <div class="m-5 d-flex flex-column align-self-center">
        <div 
        style="min-width:263.5px;"
         class=" d-flex gap-2  align-items-center align-self-center">
          <button @click="saveNoteCard" class="button btn btn-primary">Save</button>
          <span class="text-success">{{ response_message }}</span>
        </div>
      </div>
        
        <!-- <span v-if="selected_notecard">{{ selected_notecard }}</span> -->



    </div>
</template>

<script>
import EditSelectedNotecard from '../components/EditSelectedNotecard.vue';
import Scroll from '../components/Scroll.vue';
import { useSelectednotecardStore } from '@/stores/selected_notecard'
import { useNotecardsStore } from '@/stores/notecards'
import { useModalStore } from '@/stores/modal'
import { useResponsemessageStore } from '@/stores/response_message'
import _ from 'lodash';

import { storeToRefs } from 'pinia'
//const store = useSelectednotecardStore();
//const { selected_notecard } = storeToRefs(store);

export default {
  data() {
    return {
      selected_notecard_store: useSelectednotecardStore(),
      response_message_store: useResponsemessageStore(),
      notecards_store: useNotecardsStore(),
      modal_store: useModalStore(),
      loading: true,
      stack: null,
      // selected_notecard: null,
    };
  },
  computed: {
    selected_notecard() {
      return this.selected_notecard_store.selected_notecard;
    },
    notecards(){
      return this.notecards_store.notecards
    },
    modal(){
      return this.modal_store.getModal
    },
    response_message(){
      return this.response_message_store.getResponseMessage
    },
    
  },
  created() {
    this.fetchStack();
  },
  methods: {
    fetchStack() {
      // Make the API call to fetch the stack data
      axios.get(`/stacks/${this.$route.params.stack_id}`)
        .then(response => {
          // Assign the fetched stack to the component data
          this.notecards_store.setNotecards(response.data.data.notecards);
          this.stack = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error(error);
        });
    },
    async saveNoteCard(){
      if (this.selected_notecard.notecard_id) {
        let res = await window.axios.put(`/api/notecards/${this.selected_notecard.notecard_id}`, this.selected_notecard);
        this.selected_notecard_store.setSelectedNotecard(res.data.data);
        this.notecards_store.repsertNotecard(res.data.data);
        this.response_message_store.setResponseMessage(res.data.message);
      } else {
        try {
          let res = await window.axios.post(`/api/notecards`, {
            front: this.selected_notecard.front,
            back: this.selected_notecard.back,
            stack_id: this.$route.params.stack_id
          });
          this.notecards_store.repsertNotecard(res.data.data)
          this.response_message_store.setResponseMessage(res.data.message);
          
        } catch (error) {
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
    },
    async editStackName(){
      try {
        let res = await window.axios.put(`/stacks/${this.stack.stack_id}`, {name: this.stack.name});
        this.response_message_store.setResponseMessage(res.data.message);
      } catch (error) {
        console.error(error);
      }

    },
    async debounceEditStackName(){
      //context of this gets lost
      let editStack = this.editStackName;
      let debouncedEdit =  _.debounce(async function()  {
        await editStack();
      }, 500);
      await debouncedEdit();
    }
  },
};
</script>
