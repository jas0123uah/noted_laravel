<template>
    <div class="modal-overlay" :style="modalStyle" @click="$emit('closeModal', null)">
        <div class="modal-container">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{title || "WARNING"}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" @click="modal_store.setModal({});" aria-label="Close"></button>
                    </div>
                    <div v-html="warning_message" class="modal-body"></div>
                    <div class="modal-footer gap-5 align-self-center">
                        <button type="button" class="button btn btn-secondary" data-bs-dismiss="modal" @click="modal_store.setModal({}); modal_function();">Confirm</button>
                        <button type="button" class="button btn btn-secondary" data-bs-dismiss="modal" @click=" modal_store.setModal({});">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useModalStore } from '@/stores/modal'

export default {
    props: {
        warning_message: {
            type: String,
            required: true
        },
        title: {
            type: String,
        },
        modal_function: {
            type: Function,
        }
    },
    data(){
        return {
            modal_store: useModalStore(),

        }
    },
    computed: {
        modalStyle() {
            return {
            display: this.warning_message ? 'flex' : 'none',
            };
        }
    }
};
</script>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-container {
    background-color: #fff;
    border-radius: 4px;
    padding: 20px;
    }

</style>
