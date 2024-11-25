import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useUiStore = defineStore("tasks", () => {
    const count = ref(0);
    function increment() {
        count.value++;
    }
    return { count, increment };
},
);
