<template>
    <div class="d-flex gap-3">
        <button @click="setResponseQuality(0); $emit('getNextCard', console.log('Response submitted'))" class="button btn">😵‍💫</button>
        <button @click="setResponseQuality(1); $emit('getNextCard', console.log('Response submitted'))" class="button btn">😓</button>
        <button @click="setResponseQuality(2); $emit('getNextCard', console.log('Response submitted'))" class="button btn">😔</button>
        <button @click="setResponseQuality(3); $emit('getNextCard', console.log('Response submitted'))" class="button btn">🙂</button>
        <button @click="setResponseQuality(4); $emit('getNextCard', console.log('Response submitted'))" class="button btn">😄</button>
        <button @click="setResponseQuality(5); $emit('getNextCard', console.log('Response submitted'))" class="button btn">💯</button>
    </div>

</template>
<script>
import { useNotecardsStore } from '@/stores/notecards'
export default {
    props: ['notecard_id'],
    data() {
        return {
            notecards_store: useNotecardsStore()
        
        };
    },
    created() {
    },
    methods: {
        async setResponseQuality(quality){
            try {
                let res = (await window.axios.put(`/api/notecards/${this.notecard_id}`, {quality})).data;
                this.notecards_store.repsertNotecard(res.data);
            } catch (error) {
                console.error(error);
            }
        }

    },
};
</script>
