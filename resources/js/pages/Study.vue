<template>
    <div class=" d-flex flex-column-reverse gap-5 align-items-center">
        <!-- {{ stack }} -->
        <study-notecards :notecards="notecards"></study-notecards>
        


    </div>

</template>
<script>
import StudyNotecards from '../components/StudyNotecards.vue';
import { useModalStore } from '@/stores/modal'

export default {
    props: [],
    data() {
        return {
            notecards: [],
            modal_store: useModalStore(),

        
        };
    },
    computed:{
        modal(){
            return this.modal_store.getModal
        },

    },
    async mounted(){
         try {
          let res = await window.axios.get(`/api/stacks/${this.$route.params.stack_id}`);
          this.notecards = res.data.data.notecards;
        } catch (error) {
            console.log(error, "ERRR")
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
    methods: {

    },
};
</script>
