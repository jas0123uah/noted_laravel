<template>
    <h1>hi</h1>
  <!-- <div>
    <h1>Stack View</h1>
    <div v-if="loading">Loading...</div>
    <div v-else>
      <h2>{{ stack.title }}</h2>
      <ul>
        <li v-for="notecard in stack.notecards" :key="notecard.id">
          {{ notecard.title }}
        </li>
      </ul>
    </div>
  </div> -->
</template>

<script>
export default {
  data() {
    return {
      loading: true,
      stack: null,
    };
  },
  created() {
    this.fetchStack();
  },
  methods: {
    fetchStack() {
      // Make the API call to fetch the stack data
      axios.get(`/api/stacks/${this.$route.params.stack_id}`)
        .then(response => {
          // Assign the fetched stack to the component data
          this.stack = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error(error);
        });
    },
  },
};
</script>
