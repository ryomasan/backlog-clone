<template>
    <div class="task">
        <div v-for="task in getTasksByStatus" :key="task.name"
            class="task border-solid border-2 border-indigo-600 h-[71px] m-[10px]">
            <!-- {{ tasks }} -->
            <!-- {{ getTasksByStatus }} -->
            {{ task.name }}
            <!-- {{ status }} -->
            <div class="tags flex gap-1">
                <div v-for="tag in task.tags" :key="tag" class="tag h-[15px] w-[35px] bg-[#A1AF2F] text-[10px]">
                    {{ tag }}
                </div>
            </div>
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
    tasks: Task[];
    status: Status;
}>();

// const tasks: Task[] = [
//     { name: "環境構築", tags: ["tag1", "tag2"], status: "未対応" },
//     { name: "トップ画面UI作成", tags: "tag1", status: "処理中" },
//     { name: "フィルター機能", tags: ["tag2", "tag3"], status: "レビュー中" },
//     { name: "タスク保存機能", tags: ["tag1", "tag3"], status: "完了" },
// ];

const getTasksByStatus = computed(() => {
    // console.log(props.tasks)
    // console.log(props.status)
    return props.tasks.filter(task => task.status === props.status.name)
})

// const addTask = (newTask: any) => {
//     tasks.push(newTask);
// }

</script>
