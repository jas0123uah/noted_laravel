import { createRouter, createWebHistory } from "vue-router";
import StackView from "@/pages/StackView.vue";
import AllStacks from "@/pages/AllStacks.vue";
import Study from "@/pages/Study.vue";

const routes = [
    // Define your routes here
    {
        path: "/stacks/:stack_id/edit",
        name: "stacks-edit",
        component: StackView,
        // You can specify additional route options here
    },
    {
        path: "/stacks/:stack_id/study",
        name: "stacks-study",
        component: Study,
        // You can specify additional route options here
    },
    {
        path: "/home/",
        name: "home",
        component: AllStacks,
        // You can specify additional route options here
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
console.log(router, "ROUTER");
console.log(router.getRoutes());

export default router;
