<template>
    <div class=" d-flex flex-column gap-5 align-items-center">
        <!-- {{ stack }} -->
        <scroll
        v-if="stack"
        
        class="align-self-center"
        :items="notecards" >
      </scroll>
      <edit-selected-notecard :key="selected_notecard.notecard_id" v-if="selected_notecard" :notecard="selected_notecard"></edit-selected-notecard>
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


import { storeToRefs } from 'pinia'
//const store = useSelectednotecardStore();
//const { selected_notecard } = storeToRefs(store);

export default {
  data() {
    return {
      selected_notecard_store: useSelectednotecardStore(),
      notecards_store: useNotecardsStore(),
      modal_store: useModalStore(),
      loading: true,
      stack: null,
      response_message: null,
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
    
  },
  watch: {
    selected_notecard(){
      //this.response_message = null
    }

  },
  created() {
    console.log("BOOOOO")
    this.fetchStack();
  },
  methods: {
    fetchStack() {
      // Make the API call to fetch the stack data
      axios.get(`/stacks/${this.$route.params.stack_id}`)
        .then(response => {
            console.log(response)
          // Assign the fetched stack to the component data
          this.notecards_store.setNotecards(response.data.data.notecards);
          this.stack = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.log("ERR")
          console.error(error);
        });
    },
    async saveNoteCard(){
      console.log(this.selected_notecard, "SELECTED")
      if (this.selected_notecard.notecard_id) {
        let res = await window.axios.put(`/api/notecards/${this.selected_notecard.notecard_id}`, this.selected_notecard);
        console.log(res);
        console.log(85)
        this.selected_notecard_store.setSelectedNotecard(res.data.data);
        this.notecards_store.repsertNotecard(res.data.data);
        this.response_message = res.data.message;


        
      } else {
        try {
          let res = await window.axios.post(`/api/notecards`, {
            front: this.selected_notecard.front,
            back: this.selected_notecard.back,
            stack_id: this.$route.params.stack_id
          });
          console.log(this.notecards_store.getNotecards, "BEFORE REPSERT")
          this.notecards_store.repsertNotecard(res.data.data)
          console.log(res, "NEW CARD")
          this.response_message = res.data.message;
          
        } catch (error) {
          console.log(error, "erar")
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
  },
};
</script>
