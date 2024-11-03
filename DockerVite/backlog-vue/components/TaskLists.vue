<template>
    <div class="task-lists h-[550px] w-[250px] bg-[#fff] z-10">
        <div class="add-task" @click="openTaskModal(status.name)">
            +課題の追加
        </div>
        <!-- <Task v-for="task in tasks" :key="task.name" :tasks="tasks" :status="status" /> -->
        <div>
            <Task :tasks="tasks" :status="status" />
        </div>
    </div>
</template>

<script setup lang="ts">

interface Task {
    name: string;
    tags: string[]; // tagsは文字列の配列
    status: string;
}

interface Status {
    id: number;
    name: string; // tagsは文字列の配列
}

const props = defineProps<{
    status: Status;
    tasks: Task[];
}>();

const emits = defineEmits(['openTaskModal']);

const openTaskModal = (status: string) => {
    emits("openTaskModal", status);
}

</script>
