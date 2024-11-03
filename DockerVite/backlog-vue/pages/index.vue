<!-- pages/index.vue -->
<template>
    <!-- <Header /> -->
    <div class="flex h-[816px] w-[1280px]">
        <InputTaskModal v-if="isOpenModal" :isOpenModal="isOpenModal" @addTask="addTask" :taskStatus="taskStatus"
            @closeTaskModal="closeTaskModal" />
        <div v-for="(status, index) in statusList" :key="index"
            class="bg-[#f0f0f0] p-[50px] text-3xl font-bold relative">
            {{ status.name }}
            <TaskLists :status="status" :tasks="tasks" @openTaskModal="openTaskModal" />
        </div>
    </div>
</template>

<script setup lang="ts">
// import { ref } from 'vue'
// const message = ref<string>("Hello world!")

interface Task {
    name: string;
    tags: any; // tagsは文字列の配列
    status: string;
}


const statusList = [
    { id: 1, name: "未対応" },
    { id: 2, name: "処理中" },
    { id: 3, name: "レビュー中" },
    { id: 4, name: "完了" }
]

const tasks = ref<Task[]>([
    { name: "環境構築", tags: ["tag1", "tag2"], status: "未対応" },
    { name: "トップ画面UI作成", tags: "tag1", status: "未対応" },
    { name: "フィルター機能", tags: ["tag2", "tag3"], status: "レビュー中" },
    { name: "タスク保存機能", tags: ["tag1", "tag3"], status: "完了" },
]);
// const tasks: Task[] = []

const isOpenModal = ref<boolean>(false)

let taskStatus = ref('未対応');

const openTaskModal = (status:string): void => {
    isOpenModal.value = true
    taskStatus.value = status
}

const closeTaskModal = (): void => {
    isOpenModal.value = false
}

// const getTasksByStatus = (statusName: string) => {
//     console.log(tasks);
//     return tasks.value.filter(task => task.status === statusName);
// };

const addTask = (newTask: any) => {
    tasks.value.unshift(newTask);
    console.log(tasks)
}

</script>