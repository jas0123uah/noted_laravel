<template>
  <div class="card bg-white" style="width: 18rem; height: 9rem; display: flex; align-items: center;">
    <i v-if="can_be_deleted" class="fa-solid fa-circle-minus" style="margin-top: 0.25rem; margin-right: 0.5rem; align-self: baseline;"></i>
    <div class="card-body d-flex align-items-center" style="margin-bottom: 14.4px;">
      <h5 class="card-title roboto fw-bold">{{ notecard.front }}</h5>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    notecard: {
      type: Object,
      required: true
    },
    can_be_deleted: {
      type: Boolean,
      default: true
    }
  },
  mounted() {
    console.log('Component mounted.')
    console.log(this.notecard)
  },
  methods: {
    deleteNotecard() {
      // Implement the logic to delete the notecard here
    }
  }
}
</script>
