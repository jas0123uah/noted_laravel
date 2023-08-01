<template>
    <modal
    :key="modal.message"
    v-if="modal.message" 
    :modal_function="modal.modal_function"
    :warning_message="modal.message" 
    :title="modal.type" />
    <div v-if="loading" class="d-flex justify-content-center">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    
  <div class="study-wrapper">
    <div @click="toggleNotecard"  class="notecard-container">
      <div class="notecard position-relative" :key="current_card_index">
        <div class="notecard-inner">
          <transition>
            <div :class="{'position-absolute': !show_front || in_transition}" :key="`${current_card_index}-front`" v-if="show_front" class="notecard-content hide-toolbar w-100">
                <quill-editor :syntax-highlight="customSyntaxHighlight" v-model:content="current_card.front"  :readOnly="true" contentType="html" theme="snow" />
            </div>
            <div v-else :class="{'position-absolute': show_front || in_transition}" class=" w-100 notecard-content hide-toolbar">
              <quill-editor :syntax-highlight="customSyntaxHighlight" :key="`${current_card_index}-back`" v-model:content="current_card.back"  :readOnly="true" contentType="html" theme="snow" />
            </div>
          </transition>
          </div>
        </div>
    </div>
    <span :class="{'invisible': !show_message}" class="mb-3">Tap or click the notecard to see the {{ show_front ? 'back' : 'front' }}. </span> 
    <template v-if="$route.params.stack_id === 'daily-stack' && new Date(this.notecards[current_card_index]?.next_repetition) < new Date(start_of_day)">
        <study-buttons @click="loading = true" :disabled="loading" @getNextCard="payload => {getNext(); loading = false;}" :notecard_id="this.notecards[current_card_index]?.notecard_id"></study-buttons>
        <span @click="displayDefs" class="align-self-start emulate-link fw-bold">Definitions</span>
    </template>
    <div class="swipe-controls">
      <button @click="getPrevious" class="btn button">&lt;</button>
      <button @click="getNext" class="btn button">&gt;</button>
    </div>
  </div>
</template>

<script>
import { useModalStore } from '@/stores/modal'
import hljs from 'highlight.js'
import 'highlight.js/styles/default.css'; // Import the desired code highlighting style
import 'highlight.js/styles/monokai-sublime.css'

hljs.configure({   // optionally configure hljs
    languages: ['javascript', 'ruby', 'python']
});

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
      loading: false,
      has_seen_back: false,
      has_flipped_back_to_front: false,
      modal_store: useModalStore(),
      study_button_defs:`<ul style="list-style-type:none; padding:0; margin:0;">
        <li>😵‍💫 - Blackout. Complete failure to recall the information.</li>
        <li>😓 - Incorrect response, but upon seeing the answer it felt familiar.</li>
        <li>😔 - Incorrect response, but upon seeing the answer it seemed easy to remember.</li>
        <li>🙂 - Correct response, but required significant effort to recall.</li>
        <li>😄 - Correct response, after some hesitation.</li>
        <li>💯 - Correct response with perfect recall.</li>
    </ul>` 
    };
  },
  computed: {
    current_card() {
        return this.notecards[this.current_card_index] || {};
    },
    modal(){
        return this.modal_store.getModal
    },
    start_of_day(){
      let today = new Date();
      today = today.setUTCHours(0,0,0,0);
      return new Date(today);
    },
    show_message(){
      return (this.show_front && !this.has_seen_back) || (!this.show_front && !this.has_flipped_back_to_front)

    }
  },
  methods: {
    customSyntaxHighlight(code, lang) {
            const highlightedCode = hljs.highlight(lang, code).value;
            return highlightedCode;
    },
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
      this.in_transition = true;
      setTimeout(() => {
        this.show_front = !this.show_front;
        if(!this.show_front) this.has_seen_back = true;
        if(this.show_front) this.has_flipped_back_to_front = true;
      }, 350); // match transition duration
  }
    },
    displayDefs(){
        this.modal_store.setModal({
          type: 'INFO',
          message: this.study_button_defs
        })

      }
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
/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.35s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
