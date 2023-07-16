<template>
    <div style="height: auto;" class="d-flex flex-column gap-4 align-items-center">
      <WarningModal
        :key="modal.message"
        v-if="modal.message" 
        :warning_message="modal.message" 
        :title="modal.type" />
      <study-notecards :notecards="notecards"></study-notecards>
    </div>
</template>
<script>
import StudyNotecards from '../components/StudyNotecards.vue';
import _ from 'lodash';
import { useModalStore } from '@/stores/modal'
import { useNotecardsStore } from '@/stores/notecards'
import { useUserStore } from '@/stores/user';


export default {
    props: [],
    data() {
        return {
            notecards: [],
            modal_store: useModalStore(), 
            notecards_store: useNotecardsStore(),
            user_store: useUserStore(),  
        };
    },
    computed:{
        modal(){
            return this.modal_store.getModal
        },  
    },
    async mounted(){
        try {
          if(this.$route.params.stack_id === 'daily-stack'){
            //May happen if the user navigates directly to daily stack via URL bar
            if(!this.notecards_store.getNotecards.length){
              let user = (await window.axios.get('/api/users/')).data;
              user.data.show_confirm_email = !user.data.email_verified_at && user.data.show_confirm_email === undefined; 
              this.user_store.setUser(user.data)
              
              
              let review_notecards = (await window.axios.get(`/api/reviewnotecards/${user.data.user_id}`)).data
              //Put the notecards that need review first at the top of the stack
              this.review_notecards = _.orderBy(review_notecards.data, nc => new Date(nc.notecard.next_repetition), ['asc']);
              
              //Go ahead and set the review notecards as the notecards in the pinia store. If the user goes to the daily stack we'll have them
              this.notecards_store.setNotecards(this.review_notecards.map(nc => nc.notecard));  
            }
            this.notecards = this.notecards_store.getNotecards
          }
          else {
            let res = await window.axios.get(`/api/stacks/${this.$route.params.stack_id}`);
            this.notecards = res.data.data.notecards;
          }
        } catch (error) {
            console.error(error);
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
    },
};
</script>
