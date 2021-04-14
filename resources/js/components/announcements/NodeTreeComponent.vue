<template>
  <p class="is-size-5">{{ node.title }}</p>

  <ul v-if="node.children && node.children.length">
    <node v-for="child in node.children" :node="child"></node>
  </ul>
</template>

<script>
export default {
  name: "node",
  props: {
    node: Object,
  },
};
</script>

<style>
</style>