<!-- pages/index.vue -->
<template>
    <!-- <Header /> -->
    <!-- <div class="flex h-[816px] w-[1280px]"> -->
    <div class="main flex flex-col bg-[#f0f0f0] p-[50px] ">
        <div class="filter">
            <p>タグ</p>
            <div @click="toggleTagFilter"
                class="filter-box flex justify-end h-[30px] w-[150px] border-solid border border-indigo-600 bg-[#fff] px-[5px]">
                ▼
            </div>
            <div v-if="isOpenFilter"
                class="filter absolute bg-[#fff] h-[207px] w-[150px] z-10 border-solid border border-indigo-600">
                <input v-model="seachedTag" type="text" class="search-box h-[30px] w-[140px] m-[5px] mb-[15px]"
                    placeholder="タグを検索">
                <div class="select-option h-[130px] overflow-y-scroll">
                    <div @click="resetTags()" class="unsigned m-[5px]">未選択<span v-if="unSelected"
                            class="tag_checked">✔︎</span></div>
                    <div @click="selectTags(index)" v-for="(tag, index) in seachedTags" :key="index"
                        class="tag-item m-[5px]">
                        <div class="tag_name">{{ tag.name }}<span v-if="tag.isSelected" class="tag_checked">✔︎</span>
                        </div>
                    </div>
                </div>
                <div @click="filterTags"
                    class="filter-btn text-center absolute w-full bg-[black] text-[white]  bottom-0">絞り込む</div>
            </div>
        </div>
        <div class="flex justify-between mt-[40px]">
            <InputTaskModal v-if="isOpenModal" :isOpenModal="isOpenModal" @addTask="addTask" :tasks="tasks" :tags="tags"
                :taskStatus="taskStatus" @closeTaskModal="closeTaskModal" />
            <div v-for="(status, index) in statusList" :key="index" class="text-3xl font-bold relative">
                <div class="task-status flex gap-2">
                    <div class="task-color w-[16px] h-[16px]" :class="taskColor(status.name)">⚫︎</div>
                    <div class="task-cotent">{{ status.name }}</div>
                    <div
                        class="task-count w-[34px] h-[21px] flex justify-center items-center bg-[#d9d9d9] rounded-full">
                        {{ taskCount(status.name) }}
                    </div>
                </div>
                <TaskLists :status="status" :tasks="tasks" :filteredTasks="filteredTasks"
                    @openTaskModal="openTaskModal" />
            </div>
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

let tasks = ref<Task[]>([]);

const tags = ref<Tag[]>([]);

const seachedTag = ref<string>('');

const isOpenFilter = ref<boolean>(false)

const isOpenModal = ref<boolean>(false)

let taskStatus = ref('未対応');


const seachedTags = computed(() => {
    return tags.value.filter(tag => tag.name.includes(seachedTag.value))
})

const toggleTagFilter = () => {
    isOpenFilter.value = !isOpenFilter.value
}

const unSelected = ref<boolean>(false)

const resetTags = () => {
    tags.value.forEach(tag => {
        tag.isSelected = false;
    });
    unSelected.value = !unSelected.value
}

const selectTags = (index: number) => {
    unSelected.value = false;
    tags.value[index].isSelected = !tags.value[index].isSelected
}

const filteredTasks = ref<Task[]>([])


let originalTasks = ref<Task[]>([])

const filterTags = () => {
    const selectedTags = tags.value.filter(tag => tag.isSelected).map(tag => tag.name);

    // タグが選択されていない場合は全てのタスクを返す
    if (selectedTags.length === 0 || unSelected) {
        filteredTasks.value = [...originalTasks.value];
    }

    // 選択されたタグを持つタスクのみを返す
    filteredTasks.value = originalTasks.value.filter(task => {
        const taskTags = task.tags.map(tag => tag.name);
        return selectedTags.every(selectTag => taskTags.includes(selectTag))
        // task.tags.some(tag => selectedTags.includes(tag.name))
    });

    if (selectedTags.length !== 0  && filteredTasks.value.length === 0) {
        tasks.value = [];
    }
}

const taskColor = (status: string) => {
    switch (status) {
        case '未対応':
            return 'text-[#ED8077]'; // Change to appropriate color
        case '処理中':
            return 'text-[#4487C5]'; // Adjust color as needed
        case 'レビュー中':
            return 'text-[#5EB5A6]'; // Adjust color as needed
        case '完了':
            return 'text-[#A1AF2F]'; // Adjust color as needed
    }
}

const openTaskModal = (status: string): void => {
    isOpenModal.value = true
    taskStatus.value = status
}

const closeTaskModal = (): void => {
    isOpenModal.value = false
}

const taskCount = (statusName: string) => {
    return filteredTasks.value.filter(task => task.status === statusName).length;
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
        tasks.value.unshift(data);
        // originalTasks.value.unshift(data);
        // tasks.value.splice(0, tasks.value.length, ...data);
        closeTaskModal();
    } catch (error: any) {
        console.error("Server responded with a status:", error);
    }
}

onMounted(async () => {
    const responseTasks = await fetch('http://localhost:8000/api/tasks');
    const responseTags = await fetch('http://localhost:8000/api/tags');
    try {
        if (!responseTags.ok || !responseTasks.ok) {
            throw new Error("Failed to fetch tags");
        }
        const tagData = await responseTags.json();
        tags.value = tagData;
        const taskData = await responseTasks.json(); // Convert response to JSON
        originalTasks.value = taskData.map((task: Task) => ({ ...task })); // Store original tasks
        // tasks.value.splice(0, tasks.value.length, ...originalTasks.value); // Clear and update the tags
        filterTags();
        // const tasksByStatus = data.reduce((acc: any, item: any) => {
        //     if (!acc[item.status]) {
        //         acc[item.status] = [];
        //     }
        //     acc[item.status].push(item);
        //     return acc;
        // }, {})
        // Object.keys(tasksByStatus).forEach(status => {
        //     tasksByStatus[status].forEach((task:any, index:number) => {
        //         task.order = index + 1;
        //     });
        // });
    }
    catch (error) {
        console.error("Server responded with a status:", error);
    }
})
</script>