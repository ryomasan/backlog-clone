<template>
    <div @click="clickModal"
        class="add-task-modal flex justify-center items-center content-center h-full w-full bg-[#000] opacity-30 fixed top-0 left-0 z-30">
        <div class="modal-content h-[330px] w-[450px] bg-[#fff]">
            <p>タスクの追加</p>
            <input v-model="inputTask" type="text" placeholder="タスクを入力...">
            <p>タグ<span @click="toggleTagList">{{ isOpenTagToggle ? '-' : '+' }}</span></p>
            <div v-if="isOpenTagToggle">
                <div class="tag-list h-[220px] w-[170px] border-solid border-2 border-indigo-600">
                    <div class="tag-header bg-[#000] h-[26px] text-center text-white">タグ一覧</div>
                    <div @click="selectTags(index)" v-for="(tag, index) in tags" :key="index"
                        class="tags flex justify-between">
                        <div class="tag_name">{{ tag.name }}</div>
                        <div v-if="tag.isSelected" class="tag_checked">✔︎</div>
                    </div>
                </div>
                <input v-model="tagName" type="text" placeholder="新しいタグを入力..."><button
                    @click="createTag(tagName)">タグ作成</button>
            </div>
            <button @click="closeTaskModal">キャンセル</button>
            <button @click="addTask">追加</button>
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

const props = defineProps<{
    isOpenModal: Boolean;
    taskStatus: string;
}>();

const isOpenTagToggle = ref(false)

// let tags = reactive([
//     { name: "tag1", isSelected: true },
//     { name: "tag2", isSelected: false },
//     { name: "tag3", isSelected: false },
// ])

const tags = reactive<Tag[]>([]);

const toggleTagList = async () => {
    isOpenTagToggle.value = !isOpenTagToggle.value
    // await axios.get('http://localhost:8000/api/tags')
    const response = await fetch('http://localhost:8000/api/tags');
    try {
        if (!response.ok) {
            throw new Error("Failed to fetch tags");
        }
        const data = await response.json(); // Convert response to JSON
        tags.splice(0, tags.length, ...data.map((tag: Tag) => ({ ...tag, isSelected: false }))); // Clear and update the tags
    }
    catch (error: any) {
        console.error("Server responded with a status:", error);
    };
}

const selectTags = (index: number) => {
    tags[index].isSelected = !tags[index].isSelected
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
            tags.push(data);
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
        const selectedTagsObj = tags.filter(tag => tag.isSelected === true);
        const selectedTags = selectedTagsObj.map(obj => Object.values(obj)[0])
        console.log(inputTask);
        newTask = {
            name: inputTask.value,
            tags: selectedTags,
            status: props.taskStatus
        };
        emits("addTask", newTask);
    } else {
        alert('タスクが入力されていません');
    }
}

</script>