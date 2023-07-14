<template>
    <scroll
        v-if="stacks"
        class="align-self-center"
        :is_stack="true"
        :items="stacks.data" >
      </scroll>
    <!-- <template v-for="stack in stacks" class=" d-flex flex-column width-fit-content">
        <div class="d-flex flex-column align-items-center" v-for="first_notecard, stack_name in stack">
            <homepage-notecard :notecard="first_notecard"></homepage-notecard>
            <stack-metadata class="align-self-center" :stack-id="first_notecard.stack_id" :stack-title="stack_name"></stack-metadata>
        </div>
    </template >   -->

</template>
<script>
import HomepageNotecard from '../components/HomepageNotecard.vue';
import StackMetadata from '../components/StackMetadata.vue';
import scroll from '../components/Scroll.vue';
export default {
    props: [],
    data() {
        return {
            stacks : [],
        
        };
    },
    async created() {
        this.stacks = (await window.axios.get('/stacks/')).data;
        console.log(this.stacks, "SSSS")

        //axios.defaults.withCredentials = true
        // Retrieve the access token when the component is created
        //this.stacks = await axios.get('/api/stacks/');
        
        //axios.defaults.withCredentials = true
    //   window.axios.get(`/stacks/`, {
    //     // headers : {
    //     //     'noted_session': 'eyJpdiI6IklwQ3pxUnREd3BYdE9IYldUTFYxbXc9PSIsInZhbHVlIjoiTEIwcGxTWXFRRzNoZnhsUVoxUXJ4TFEwbGJ6UFRncUVLU095aHBtYVVKajRIZ0VwRVdqSUI5U0JvZ2phOTJWNmNXajFibjRKTDd1UFBxbm11b3UzZFptSVU5bjYxSkFDSzJEeXNzZUVIdDExQ1J0SVh4Q1NWcjhjWnl2R0NmSm0iLCJtYWMiOiIyYzYxNmRlMDkwNWRiMmQxMDA1NTY3NzZiNjRjMWI3MTFmZmVmN2UxNWVjODA4MTQ1ODcwNTI4YTQxMmYyZTBhIiwidGFnIjoiIn0='
    //     // }
    //   })
    //     .then(response => {
    //       const stack = response.data;
    //       console.log(stack, 'LOOK')
    //       // On success, navigate to the stack view
    //       const stackURL = `/stacks/${stack.data.stack_id}`;
    //     })
    //     .catch(error => {
    //       // Handle the error case
    //       console.log("ERROR")
    //       console.error(error);
    //     });
        
    //     console.log(this.stacks, "JAY")

    },
    methods: {

    },
};
</script>
