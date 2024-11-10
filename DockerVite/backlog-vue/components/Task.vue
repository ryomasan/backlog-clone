<template>
    <div>
        <draggable v-model="localTasks" draggable=".task-content" item-key="name" @end="onDragEnd" group="tasks"
            :animation="300" :class="props.status.name" >
        <template #item="{ element: task }">
            <div class="task-content border-solid border-2 border-indigo-600 h-[71px] m-[10px] relative">
                {{ task.name }}
                <div class="tags flex flex-wrap gap-1 overflow-scroll">
                    <div v-for="tag in task.tags" :key="tag.id"
                        class="tag h-[15px] min-w-[35px] bg-[#A1AF2F] text-[#fff] text-[10px] rounded-full px-[5px]">
                        {{ tag.name }}
                    </div>
                </div>
                <font-awesome-icon @click="deleteTask(task.id)" icon="trash-can"
                    class="absolute bottom-0 right-0 p-[5px]" />
            </div>
        </template>
        </draggable>
    </div>
</template>

<script setup lang="ts">
import draggable from "vuedraggable";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
interface Tag {
    id: number;
    name: string;
    isSelected?: boolean;
}

interface Task {
    id: number;
    name: string;
    tags: Tag[];
    status: string;
    order: number;
}

interface Status {
    id: number;
    name: string;
}

const props = defineProps<{
    tasks: Task[];
    filteredTasks: Task[];
    status: Status;
}>();

const getTasksByStatus = computed(() => {
    return props.filteredTasks.length > 0
        ? props.filteredTasks.filter(task => task.status === props.status.name)
        : props.tasks.filter(task => task.status === props.status.name);
})

const localTasks = ref<Task[]>([]);

watch(getTasksByStatus, (newTasks) => {
    localTasks.value = [...newTasks];
}, { immediate: true });

const onDragEnd = async (event: any) => {
    const movedTask = event.item._underlying_vm_;
    const newOrder = event.newIndex + 1;
    const newStatus = event.to.classList[0];
    movedTask.status = newStatus;
    movedTask.order = newOrder;

    try {
        const updatedTasks = {
            status: newStatus,
            order: newOrder
        };

        const response = await fetch(`http://localhost:8000/api/update-task-status/${movedTask.id}`, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json' // 送信するデータの形式
            },
            body: JSON.stringify(updatedTasks)
        });
        if (!response.ok) {
            throw new Error("Failed to fetch tags");
        }
        const tasksResponse = await fetch('http://localhost:8000/api/tasks');
        const tasksData = await tasksResponse.json();
        
        if (!tasksResponse.ok) {
            throw new Error("Failed to fetch updated tasks");
        }

    }
    catch (error: any) {
        console.error("Server responded with a status:", error);
    };
}

const emits = defineEmits(['deleteTask'])

const deleteTask = async (taskId: number) => {
    emits('deleteTask', taskId)
}

</script>