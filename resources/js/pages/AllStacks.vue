<template>
    <div class="d-flex flex-column align-items-center gap-5">
        <modal
            @eventSuccess="(payload) => (res_message = payload)"
            :key="modal.title"
            v-if="modal.title"
            :modal_function="modal.modal_function"
            :message="modal.message"
            input_placeholder="Stack name..."
            :title="modal.title"
        />

        <div
            v-if="user.show_confirm_email && user.email !== 'demo@example.com'"
            style="position: relative"
        >
            <button
                @click="closeConfirmEmail"
                type="button"
                class="close position-relative x-button x-color"
                data-dismiss="alert"
                aria-label="Close"
            >
                <span aria-hidden="true">&times;</span>
            </button>
            <div
                class="alert alert-warning alert-dismissible fade show"
                role="alert"
            >
                A confirmation email should be in your inbox from a sender named
                Noted. Please confirm your email address: {{ user.email }}, to
                receive emails with daily review notecards.<br /><br />
                Don't see an email?
                <strong class="emulate-link" @click="sendEmailVerificationLink"
                    >Resend verification email</strong
                >.
            </div>
        </div>
        <div v-else-if="user.is_unsubscribed">
            <button
                type="button"
                class="close position-relative x-button x-color"
                data-dismiss="alert"
                aria-label="Close"
            >
                <span aria-hidden="true">&times;</span>
            </button>
            <div
                class="alert alert-warning alert-dismissible fade show"
                role="alert"
            >
                You are currently not subscribed to receive daily emails with
                reminders about your review notecards. Please
                <strong class="emulate-link" @click="subscribe"
                    >subscribe</strong
                >
                to receive emails with daily review notecards at
                {{ user.email }}.
            </div>
        </div>

        <div v-else-if="!user.is_unsubscribed && was_unsubscribed">
            <button
                type="button"
                @click="was_unsubscribed = false"
                class="close position-relative x-button x-color"
                data-dismiss="alert"
                aria-label="Close"
            >
                <span aria-hidden="true">&times;</span>
            </button>
            <div
                class="alert alert-warning alert-dismissible fade show"
                role="alert"
            >
                Subscribed to receive daily emails successfully!
            </div>
        </div>

        <div v-if="display_confirm_email_sent" class="position-relative">
            <button
                type="button"
                @click="display_confirm_email_sent = false"
                class="close close-button x-button x-color position-relative"
                data-dismiss="alert"
                aria-label="Close"
            >
                <span aria-hidden="true">&times;</span>
            </button>
            <div
                class="alert alert-warning alert-dismissible fade show d-flex justify-content-between align-items-center"
                role="alert"
            >
                <span class="alert-text"
                    >Confirmation email sent to {{ user.email }}</span
                >
            </div>
        </div>

        <div v-if="loading_stacks" class="spinner-border" role="status"></div>
        <div v-else class="align-self-center">
            <scroll v-if="stacks" :is_stack="true" :items="stacks"> </scroll>
        </div>

        <p v-if="response_message && !_.isEmpty(response_message)" class="text-success m-2 text-center">
            {{ response_message }}
        </p>
        <div v-if="loading_review" class="spinner-border" role="status"></div>
        <div
            class="d-flex flex-column align-items-center"
            v-else-if="review_notecards?.length"
        >
            <div class="d-flex gap-5">
                <i
                    v-if="stacks?.length <= 1"
                    style="line-height: normal; visibility: hidden"
                    class="fa-solid fa-2xl fa-circle-plus hover-pointer"
                ></i>
                <div>
                    <h5>Review notecards for {{ review_stack_date }}</h5>
                    <div class="d-flex gap-5">
                        <notecard
                            :item="review_notecards[0].notecard"
                            :can_be_deleted="false"
                        ></notecard>
                    </div>
                    <stack-metadata
                        :stack-title="review_stack_title"
                        stack-id="daily-stack"
                        :study_only="true"
                    ></stack-metadata>
                </div>
            </div>
        </div>
        <h5 v-else>No review notecards for {{ review_stack_date }}</h5>
    </div>
</template>
<script>
import scroll from "../components/Scroll.vue";
import { useModalStore } from "@/stores/modal";
import { useStacksStore } from "@/stores/stacks";
import { useResponsemessageStore } from "@/stores/response_message";
import { useUserStore } from "@/stores/user";
import { useNotecardsStore } from "@/stores/notecards";

import { DateTime } from "luxon";
import _ from "lodash";

export default {
    props: [],
    data() {
        return {
            modal_store: useModalStore(),
            stacks_store: useStacksStore(),
            response_message_store: useResponsemessageStore(),
            user_store: useUserStore(),
            notecards_store: useNotecardsStore(),
            display_confirm_email_sent: false,
            review_notecards: null,
            loading_review: true,
            loading_stacks: true,
            was_unsubscribed: false,
        };
    },
    async created() {
        try {
            let stacks = (await window.axios.get("/stacks/")).data;
            let user = (await window.axios.get("/api/users/")).data;
            this.was_unsubscribed = user.data.is_unsubscribed;
            this.stacks_store.setStacks(stacks.data);
            user.data.show_confirm_email =
                !user.data.email_verified_at &&
                user.data.show_confirm_email === undefined;
            this.user_store.setUser(user.data);

            let review_notecards = (
                await window.axios.get(
                    `/api/reviewnotecards/${user.data.user_id}`
                )
            ).data;
            //Put the notecards that need review first at the top of the stack
            this.review_notecards = _.orderBy(
                review_notecards.data,
                (nc) => new Date(nc.notecard.next_repetition),
                ["asc"]
            );

            //Go ahead and set the review notecards as the notecards in the pinia store. If the user goes to the daily stack we'll have them
            this.notecards_store.setNotecards(
                this.review_notecards.map((nc) => nc.notecard)
            );
            this.loading_review = false;
            this.loading_stacks = false;
        } catch (error) {
            console.error(error);
            this.loading_review = false;
            this.loading_stacks = false;
        }
    },
    computed: {
        modal() {
            return this.modal_store.getModal;
        },
        user() {
            return this.user_store.getUser;
        },
        stacks() {
            return this.stacks_store.getStacks;
        },
        response_message() {
            return this.response_message_store.getResponseMessage;
        },
        review_stack_date() {
            if (this?.review_notecards[0])
                return new DateTime(
                    this.review_notecards[0].created_at
                ).toLocaleString({ month: "long", day: "numeric" });
            return new DateTime(new Date()).toLocaleString({
                month: "long",
                day: "numeric",
            });
        },
        review_stack_title() {
            return `${this.review_stack_date} Review`;
        },
    },
    methods: {
        closeConfirmEmail() {
            this.user_store.hideConfirmEmail();
        },
        async sendEmailVerificationLink() {
            try {
                let res = await window.axios.post(
                    "/email/verification-notification"
                );
                if (res.status === 200) {
                    this.user_store.hideConfirmEmail();
                    this.display_confirm_email_sent = true;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async subscribe() {
            try {
                let res = await window.axios.get(
                    `/subscribe/${this.user.subscription_token}`
                );
                console.log(res);
                console.log(res.status);
                if (res.status === 200) {
                    this.user_store.updateProp("is_unsubscribed", false);
                }
            } catch (error) {
                console.error(error);
            }
        },
    },
};
</script>
