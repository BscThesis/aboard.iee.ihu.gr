<template>
  <span class="icon is-clickable" @click="open = !open">
    <i v-if="open" class="fas fa-caret-down"></i>
    <i v-else class="fas fa-caret-right"></i>
  </span>
</template>

<script>
export default {};
</script>

<style>
</style>