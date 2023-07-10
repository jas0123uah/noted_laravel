import { createRouter, createWebHistory } from "vue-router";
import StackView from "@/components/StackView.vue";
console.log(StackView, "STACK")
const routes = [
    // Define your routes here
    {
        path: "/stacks/:stack_id",
        component: StackView,
        // You can specify additional route options here
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
