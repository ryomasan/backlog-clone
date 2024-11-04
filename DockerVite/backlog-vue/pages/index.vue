<!-- pages/index.vue -->
<template>
    <!-- <Header /> -->
    <div class="flex h-[816px] w-[1280px]">
        <InputTaskModal v-if="isOpenModal" :isOpenModal="isOpenModal" @addTask="addTask" :taskStatus="taskStatus"
            @closeTaskModal="closeTaskModal" />
        <div v-for="(status, index) in statusList" :key="index"
            class="bg-[#f0f0f0] p-[50px] text-3xl font-bold relative">
            {{ status.name }}
            <TaskLists :status="status" :tasks="tasks" @openTaskModal="openTaskModal"/>
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
    name: string;
    tags: Tag[];
    status: string;
}

const statusList = [
    { id: 1, name: "未対応" },
    { id: 2, name: "処理中" },
    { id: 3, name: "レビュー中" },
    { id: 4, name: "完了" }
]

// let tasks = ref<Task[]>([
//     { name: "環境構築", tags: ["tag1", "tag2"], status: "未対応" },
//     { name: "トップ画面UI作成", tags: "tag1", status: "未対応" },
//     { name: "フィルター機能", tags: ["tag2", "tag3"], status: "レビュー中" },
//     { name: "タスク保存機能", tags: ["tag1", "tag3"], status: "完了" },
// ]);
let tasks = reactive<Task[]>([]);

const isOpenModal = ref<boolean>(false)

let taskStatus = ref('未対応');

const openTaskModal = (status: string): void => {
    isOpenModal.value = true
    taskStatus.value = status
}

const closeTaskModal = (): void => {
    isOpenModal.value = false
}

const addTask = async (newTask: Task) => {
    const getCircularReplacer = () => {
        const seen = new WeakSet();
        return (key: string, value: any) => {
            if (typeof value === "object" && value !== null) {
                if (seen.has(value)) {
                    return; // 循環参照の場合、undefinedを返す
                }
                seen.add(value);
            }
            return value;
        };
    };

    const response = await fetch('http://localhost:8000/api/tasks', {
        method: "POST",
        headers: {
            'Content-Type': 'application/json' // 送信するデータの形式
        },
        body: JSON.stringify(newTask, getCircularReplacer())
    });
    try {
        if (!response.ok) {
            throw new Error("Failed to fetch tasks");
        }
        const data = await response.json();
        tasks.unshift(data);
        // tasks.splice(0, tasks.length, ...data);
        closeTaskModal();
    } catch (error: any) {
        console.error("Server responded with a status:", error);
    }
}

onMounted(async () => {
    const response = await fetch('http://localhost:8000/api/tasks');
    try {
        if (!response.ok) {
            throw new Error("Failed to fetch tags");
        }
        const data = await response.json(); // Convert response to JSON
        console.log(data);
        tasks.splice(0, tasks.length, ...data.map((task: Task) => ({ ...task }))); // Clear and update the tags
    }
    catch (error) {
        console.error("Server responded with a status:", error);
    }
})
</script>