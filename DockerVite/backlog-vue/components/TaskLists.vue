<template>
    <div class="task-lists h-[549px] w-[250px] bg-[#fff] z-10 mt-[20px] p-[10px]">
        <div class="add-task" @click="openTaskModal(status.name)">
            +課題の追加
        </div>
        <!-- <Task v-for="task in tasks" :key="task.name" :tasks="tasks" :status="status" /> -->
        <div class="h-[500px] mt-[10px] mb-[10px] overflow-y-scroll">
            <Task :tasks="tasks" :filteredTasks="filteredTasks" :status="status" @deleteTask="deleteTask" />
        </div>
    </div>
</template>

<script setup lang="ts">
interface Tag {
    id: number,
    name: string,
    isSelected?: boolean
}

interface Task {
    id: number,
    name: string;
    tags: Tag[];
    status: string;
    order: number,
}

interface Status {
    id: number;
    name: string;
}

const props = defineProps<{
    status: Status;
    tasks: Task[];
    filteredTasks: Task[];
}>();

const emits = defineEmits(['openTaskModal', 'deleteTask']);

const openTaskModal = (status: string) => {
    emits("openTaskModal", status);
}

const deleteTask = (taskId: number) => {
    emits('deleteTask', taskId)
}
</script>
