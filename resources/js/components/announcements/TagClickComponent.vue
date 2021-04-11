<template>
  <span class="icon is-clickable" @click="clicked = !clicked">
    <i v-if="clicked" class="far fa-check-square"></i>
    <i v-else class="far fa-square"></i>
  </span>
</template>

<script>
export default {};
</script>

<style>
</style>