<template>
    <div class="task">
        <draggable v-model="getTasksByStatus" draggable=".task-content" :group="tasks" item-key="name"
            @start="onDragStart" @end="onDragEnd">
            <template #item="{ element: task }">
                <div class="task-content border-solid border-2 border-indigo-600 h-[71px] m-[10px]">
                    {{ task.name }}
                    <div class="tags flex flex-wrap gap-1 overflow-scroll">
                        <div v-for="tag in task.tags" :key="tag.id"
                            class="tag h-[15px] min-w-[35px] bg-[#A1AF2F] text-[#fff] text-[10px] rounded-full px-[5px]">
                            {{ tag.name }}
                        </div>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script setup lang="ts">
import draggable from "vuedraggable";

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
}

interface Status {
    id: number;
    name: string; // tagsは文字列の配列
}

const props = defineProps<{
    tasks: Task[];
    filteredTasks: Task[];
    status: Status;
}>();

const localTasks = ref([...props.tasks]);

// const tasks: Task[] = [
//     { name: "環境構築", tags: ["tag1", "tag2"], status: "未対応" },
//     { name: "トップ画面UI作成", tags: "tag1", status: "処理中" },
//     { name: "フィルター機能", tags: ["tag2", "tag3"], status: "レビュー中" },
//     { name: "タスク保存機能", tags: ["tag1", "tag3"], status: "完了" },
// ];

const onDragStart = (event: any) => {
    console.log(event);
}

const onDragEnd = async (event: any, taskId: number) => {
    const movedTask = event.item._underlying_vm_;
    console.log(movedTask);
    taskId = movedTask.id;
    // const oldOrder = event.oldIndex;
    const newOrder = event.newIndex;
    // const oldStatus = event.item._underlying_vm_.status;
    const newStatus = props.status.name;  // ドロップされたステータス列

    console.log(newStatus);
    // if(newOrder > oldOrder){

    // }
    const updatedTasks = {
        id: taskId,
        status: newStatus,
        order: newOrder
    };

    const response = await fetch(`http://localhost:8000/api/tasks/${taskId}`, {
        method: "PUT",
        headers: {
            'Content-Type': 'application/json' // 送信するデータの形式
        },
        body: JSON.stringify(updatedTasks)
    });
    try {
        if (!response.ok) {
            throw new Error("Failed to fetch tags");
        }
        const data = await response.json(); // Convert response to JSON
        // tags.value.splice(0, tags.value.length, ...data.map((tag: Tag) => ({ ...tag, isSelected: false }))); // Clear and update the tags
    }
    catch (error: any) {
        console.error("Server responded with a status:", error);
    };
}

const getTasksByStatus = computed(() => {
    if (props.filteredTasks.length > 0) {
        return props.filteredTasks.filter(task => task.status === props.status.name);     
    } else {
        return props.tasks.filter(task => task.status === props.status.name)
    }
})
// const getTasksByStatus = computed(() => {
//     return props.filteredTasks.filter(task => task.status === props.status.name);
// });

</script>
