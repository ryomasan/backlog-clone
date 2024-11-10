<script setup lang="ts">
// export default Header({
//     layout: false
// })
</script>
<template>
    <div class="header h-[832px] w-[1280px] bg-white">
        <h1 class="h-[71px] flex flex-col justify-center font-bold text-[30px] text-right mr-10">Todo</h1>
        <slot />   
    </div>
    
</template>