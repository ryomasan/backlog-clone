<template>
    <div @click="clickModal"
        class="add-task-modal flex justify-center items-center content-center h-full w-full bg-[rgba(0,0,0,0.3)] fixed top-0 left-0 z-30">
        <div class="modal-content w-[450px] flex flex-col items-center justify-center bg-[#fff] opacity-100"
            :class='isOpenTagToggle ? "h-[522px]" : "h-[330px]"'>
            <div class="text-center my-[20px]">タスクの追加</div>
            <div class="add-task">
                <input v-model="inputTask" class="h-[51px] w-[316px]" type="text" placeholder="タスクを入力...">
                <p>タグ<span @click="toggleTagList">{{ isOpenTagToggle ? '-' : '+' }}</span></p>
            </div>
            <div v-if="isOpenTagToggle" class="mr-[80px]">
                <div class="tag-list h-[220px] w-[170px] border-solid border-2 border-indigo-600">
                    <div class="tag-header bg-[#000] h-[26px] text-center text-[#fff]">タグ一覧</div>
                    <div @click="selectTags(index)" v-for="(tag, index) in props.tags" :key="index"
                        class="tags flex justify-between">
                        <div class="tag_name">
                            {{ tag.name }}<span v-if="tag.isSelected" class="tag_checked">✔︎</span>
                        </div>
                    </div>
                </div>
                <div class="create-tag mt-[5px]">
                    <input v-model="tagName" type="text" placeholder="新しいタグを入力..."><button
                        @click="createTag(tagName)">タグ作成</button>
                </div>
            </div>
            <div class="button-part flex gap-3  mr-[200px]" :class="isOpenTagToggle ? 'mt-[20px]' : 'mt-[87px]'">
                <button @click="closeTaskModal">キャンセル</button>
                <button @click="addTask">追加</button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import axios from 'axios';

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

const props = defineProps<{
    isOpenModal: Boolean;
    taskStatus: string;
    tasks: Task[];
    tags: Tag[];
}>();

const isOpenTagToggle = ref(false)

// let tags = reactive([
//     { name: "tag1", isSelected: true },
//     { name: "tag2", isSelected: false },
//     { name: "tag3", isSelected: false },
// ])

// const tags = ref<Tag[]>([]);

const toggleTagList = async () => {
    isOpenTagToggle.value = !isOpenTagToggle.value
}

const selectTags = (index: number) => {
    props.tags[index].isSelected = !props.tags[index].isSelected
}

const inputTask = ref<string>("");
const tagName = ref<string>("");

const createTag = async (tagName: string) => {
    if (tagName.trim()) {
        const newTag = {
            name: tagName,
        };
        const response = await fetch('http://localhost:8000/api/tags', {
            method: "POST",
            headers: {
                'Content-Type': 'application/json' // 送信するデータの形式
            },
            body: JSON.stringify(newTag) // 送信するデータをJSON形式に変換
        });
        try {
            console.log(response);
            if (!response.ok) {
                throw new Error("Failed to fetch tags");
            }
            const data = await response.json();
            props.tags.push(data);
            // tags.splice(0, tags.length, ...data);
        } catch (error: any) {
            console.error("Server responded with a status:", error);
        }
    }
}

const emits = defineEmits(['closeTaskModal', 'addTask']);

const closeTaskModal = (): void => {
    emits("closeTaskModal");
}

const clickModal = (e: any) => {
    if (e.target.classList.contains("add-task-modal")) {
        closeTaskModal();
    }
}

const addTask = (newTask: Object): void => {
    if (inputTask) {
        const selectedTagsObj = props.tags.filter(tag => tag.isSelected === true);
        const selectedTags = selectedTagsObj.map(obj => Object.values(obj)[0])
        newTask = {
            name: inputTask.value,
            tags: selectedTags,
            status: props.taskStatus,
            order: 1
        };
        emits("addTask", newTask);
    } else {
        alert('タスクが入力されていません');
    }
}

</script>