<script setup lang="ts">
// export default Header({
//     layout: false
// })
</script>
<template>
    <div class="header h-[71px] w-[1280px] bg-white">
        <h1 class="h-full flex flex-col justify-center font-bold text-[30px] text-right mr-10">Todo</h1>
        <slot />
    </div>
</template>