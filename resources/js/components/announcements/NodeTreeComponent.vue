<template>
  <div>
    <div v-if="node.title" class="panel-block tags-panel-block">
      <div class="columns is-mobile">
        <div class="column is-offset-0 is-narrow">
          <tag-caret></tag-caret>
        </div>
        <div class="column is-narrow">
          <tag-click></tag-click>
        </div>
        <div class="column is-narrow">
          <span class="is-unselectable">{{ node.title }}</span>
        </div>
      </div>
    </div>

    <ul v-if="node.children && node.children.length">
      <node v-for="child in node.children" :node="child" :key="node.id"></node>
    </ul>
  </div>
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