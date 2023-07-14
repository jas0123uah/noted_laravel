<template>
    <WarningModal
    :key="modal.message"
    v-if="modal.message" 
    :modal_function="modal.modal_function"
    :warning_message="modal.message" 
    :title="modal.type" />
  <div class="study-wrapper">
    <div @click="toggleNotecard"  class="notecard-container">
      <transition name="notecard-transition">
        <div class="notecard" :key="current_card_index">
          <div class="notecard-inner">
            <div :key="`${current_card_index}-front`" v-if="show_front" class="notecard-content hide-toolbar">
              <quill-editor v-model:content="current_card.front"  readOnly="true" contentType="html" theme="snow" />
            </div>
            <div v-else class="notecard-content hide-toolbar">
              <quill-editor :key="`${current_card_index}-back`" v-model:content="current_card.back"  readOnly="true" contentType="html" theme="snow" />
            </div>
          </div>
        </div>
      </transition>
    </div>
    <div class="swipe-controls">
      <button @click="getPrevious" class="btn button">&lt;</button>
      <button @click="getNext" class="btn button">&gt;</button>
    </div>
  </div>
</template>

<script>
import { useModalStore } from '@/stores/modal'

export default {
  props: {
    notecards: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      current_card_index: 0,
      show_front: true,
      modal_store: useModalStore(),
    };
  },
  computed: {
    current_card() {
        return this.notecards[this.current_card_index] || {};
    },
    modal(){
        return this.modal_store.getModal
    },
  },
  methods: {
    getPrevious() {
      if (this.current_card_index > 0) {
        this.current_card_index--;
      } else {
        this.current_card_index = this.notecards.length - 1;
      }
      this.show_front = true;
    },
    getNext() {
      if (this.current_card_index < this.notecards.length - 1) {
        this.current_card_index++;
      } else {
        this.current_card_index = 0;
      }
      this.show_front = true;
    },
    toggleNotecard() {
      this.show_front = !this.show_front;
    },
  },
};
</script>

<style scoped>
.study-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100vh;
}

.notecard-container {
  position: relative;
  width: 300px;
  height: 200px;
  margin-bottom: 20px;
}

.notecard {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.notecard-inner {
  width: 100%;
  height: 100%;
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease-in-out;
}

.notecard-content {
  width: 100%;
  height: 100%;
}

.swipe-controls {
  display: flex;
  justify-content: center;
}

button {
  margin: 0 5px;
  padding: 10px 20px;
  font-size: 18px;
  border: none;
  background-color: #ccc;
  cursor: pointer;
}
</style>
