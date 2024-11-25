import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useTagStore = defineStore(
    "counter",
    () => {
        const count = ref(0);
        function increment() {
            count.value++;
        }
        return { count, increment };
    },
);
