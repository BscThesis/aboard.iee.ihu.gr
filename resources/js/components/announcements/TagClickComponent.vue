<template>
  <span class="icon is-clickable" @click="clicked = !clicked">
    <i v-if="clicked" class="far fa-check-square"></i>
    <i v-else class="far fa-square"></i>
  </span>
</template>

<script>
export default {
  data: function () {
    return {
      clicked: false,
    };
  },
};
</script>

<style>
</style>