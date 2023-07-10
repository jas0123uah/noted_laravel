<template>
  <div class="d-flex flex-column">
    <span>Stack ID: {{ accessToken }}</span>
    <span class="align-self-center">{{ stackTitle }}</span>
    <div class="d-flex flex-row gap-5">
      <button class="btn button text-white border-0 p-2">Study</button>
      <button class="btn button text-white border-0 p-2" @click="editStack">Edit Stack</button>
    </div>
  </div>
</template>

<script>
export default {
  props: ['stackTitle', 'stackId'],
  data() {
    return {
        accessToken: ''
    };
  },
  created() {
    // Retrieve the access token when the component is created
    this.accessToken = localStorage.getItem('access_token');
  },
  methods: {
    editStack() {
      console.log("LINE 26")
      // Make an API call using stackID
      // Replace the placeholder API call with your actual implementation
      this.$router.push(`/stacks/${this.stackId}`);
      // axios.defaults.withCredentials = true
      // axios.get(`/stacks/${this.stackId}`)
      //   .then(response => {
      //     const stack = response.data;
      //     console.log(stack)
      //     // On success, navigate to the stack view
      //     const stackURL = `/stacks/${stack.data.stack_id}`;
      //   })
      //   .catch(error => {
      //     // Handle the error case
      //     console.error(error);
      //   });
    },
  },
};
</script>
