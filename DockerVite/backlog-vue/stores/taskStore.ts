import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useTaskStore = defineStore("tasks", () => {
    const count = ref(0);
    function increment() {
        count.value++;
    }
    return { count, increment };
},
);
