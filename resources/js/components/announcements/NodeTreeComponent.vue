<template>
  <div>
    <p class="is-size-5">{{ node[0].name }}</p>

    <ul v-if="node.children && node.children.length">
      <node v-for="child in node.children" :node="child" :key="node.id"></node>
    </ul>
  </div>
</template>

<script>
export default {
  name: "node",
  props: {
    node: Array,
  },
};
</script>

<style>
</style>