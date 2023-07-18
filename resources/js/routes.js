import { createRouter, createWebHistory } from "vue-router";
import StackView from "@/pages/StackView.vue";
import AllStacks from "@/pages/AllStacks.vue";
import Study from "@/pages/Study.vue";

const routes = [
    {
        path: "/stacks/:stack_id/edit",
        name: "stacks-edit",
        component: StackView,
    },
    {
        path: "/stacks/:stack_id/study",
        name: "stacks-study",
        component: Study,
    },
    {
        path: "/home/",
        name: "home",
        component: AllStacks,
    },
    {
        path: '/demo/',
        name: "demo",
        redirect: '/home/'

    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
