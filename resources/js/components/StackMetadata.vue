<template>
  <div class="d-flex flex-column">
    <span class="align-self-center">{{ stackTitle }}</span>
    <div class="d-flex flex-row gap-5">
      <button class="btn button text-white border-0 p-2" @click="studyStack">Study</button>
      <button :class="{'invisible': study_only}" class="btn button text-white border-0 p-2" @click="editStack">Edit Stack</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    'stackTitle': {
      type:String

    }, 
    'stackId': {
      required:true,
    },
    'study_only': {
      type: Boolean,
      default: false

    }
  },
  data() {
    return {
    };
  },
  created() {
  },
  methods: {
    editStack() {
      this.$router.push(`/stacks/${this.stackId}/edit`);
    },
    studyStack() {
      this.$router.push(`/stacks/${this.stackId}/study`);
    },
  },
};
</script>
