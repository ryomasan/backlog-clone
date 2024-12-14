<!-- pages/index.vue -->
<template>
    <div class="main flex flex-col bg-[#f0f0f0] p-[50px]">
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
                    <div @click.stop="selectTags(index)" v-for="(tag, index) in searchedTags" :key="index"
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
            <div v-for="(status, index) in statusList" :key="index" class="text-3xl font-bold relative">
                <div class="task-status flex gap-2">
                    <div class="task-color w-[16px] h-[16px]" :class="taskColor(status.name)">⚫︎</div>
                    <div class="task-cotent">{{ status.name }}</div>
                    <div
                        class="task-count w-[34px] h-[21px] flex justify-center items-center bg-[#d9d9d9] rounded-full">
                        {{ taskCount(status.name) }}
                    </div>
                </div>
                <TaskLists :status="status" :tasks="tasks" :filteredTasks="filteredTasks" @openTaskModal="openTaskModal"
                    @deleteTask='deleteTask' :class="status" />
            </div>
        </div>
    </div>
    <InputTaskModal v-if="isOpenModal" :isOpenModal="isOpenModal" @addTask="addTask" :tasks="tasks" :tags="tags"
        :taskStatus="taskStatus" @closeTaskModal="closeTaskModal" />
</template>

<script setup lang="ts">
import draggable from "vuedraggable";

interface Tag {
    id: number,
    name: string,
    isSelected: boolean
}

interface Task {
    id: number,
    name: string;
    tags: Tag[];
    status: string;
    order: number;
}

const statusList = [
    { id: 1, name: "未対応" },
    { id: 2, name: "処理中" },
    { id: 3, name: "レビュー中" },
    { id: 4, name: "完了" }
]

const tasks = ref<Task[]>([]);

const tags = ref<Tag[]>([]);

const seachedTag = ref<string>('');

const isOpenFilter = ref<boolean>(false)

const isOpenModal = ref<boolean>(false)

let taskStatus = ref('未対応');

const searchedTags = computed(() => {
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
    if (tags.value[index].isSelected == undefined) {
        tags.value[index].isSelected = true
    };
    tags.value[index].isSelected = !tags.value[index].isSelected
    unSelected.value = false;
}

const filteredTasks = ref<Task[]>([])

let originalTasks = ref<Task[]>([])

const filterTags = (event: any) => {
    event.preventDefault();
    const selectedTags = tags.value.filter(tag => tag.isSelected).map(tag => tag.name);

    // タグが選択されていない場合は全てのタスクを返す
    if (selectedTags.length === 0 || unSelected) {
        filteredTasks.value = [...originalTasks.value];
    }

    // 選択されたタグを持つタスクのみを返す
    filteredTasks.value = originalTasks.value.filter(task => {
        const taskTags = task.tags.map(tag => tag.name);
        return selectedTags.every(selectTag => taskTags.includes(selectTag))
    });

    if (selectedTags.length !== 0 && filteredTasks.value.length === 0) {
        tasks.value = [];
    }
}

const taskColor = (status: string) => {
    switch (status) {
        case '未対応':
            return 'text-[#ED8077]';
        case '処理中':
            return 'text-[#4487C5]';
        case 'レビュー中':
            return 'text-[#5EB5A6]';
        case '完了':
            return 'text-[#A1AF2F]';
    }
}


const openTaskModal = (status: string): void => {
    isOpenModal.value = true
    taskStatus.value = status
}

const deleteTask = async (taskId: number) => {
    const deletedTasks = {
        id: taskId,
    };

    const response = await fetch(`http://localhost:8000/api/delete-task/${taskId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json' // 送信するデータの形式
        },
        body: JSON.stringify(deletedTasks)
    });
    try {
        if (!response.ok) {
            throw new Error("Failed to fetch tags");
        }
        const data = await response.json(); // Convert response to JSON
        originalTasks.value = originalTasks.value.filter(task => task.id != taskId);
        tasks.value = [...originalTasks.value];
        console.log(tasks);
    }
    catch (error: any) {
        console.error("Server responded with a status:", error);
    };
}

const closeTaskModal = (): void => {
    isOpenModal.value = false
}

const taskCount = (statusName: string) => {
    return tasks.value.filter(task => task.status === statusName).length;
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

    try {
        const response = await fetch('http://localhost:8000/api/register-tasks', {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(newTask, getCircularReplacer())
        });
        if (!response.ok) {
            throw new Error("Failed to fetch tasks");
        }
        const data = await response.json();
        tasks.value.unshift(data);
        originalTasks.value.unshift(data)
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
        const taskData = await responseTasks.json();
        tasks.value = taskData;
        originalTasks.value = taskData.map((task: Task) => ({ ...task }));
    }
    catch (error) {
        console.error("Server responded with a status:", error);
    }
})
</script>