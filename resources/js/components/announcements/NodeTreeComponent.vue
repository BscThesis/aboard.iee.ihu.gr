<template>
  <div v-if="node.children && node.children.length">
    <div v-if="node.title" class="panel-block tags-panel-block ml-20">
      <div class="columns is-mobile">
        <div class="column is-narrow" v-bind:class="classObject">
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

    <!-- <ul v-if="node.children && node.children.length"> -->
    <node
      v-for="(child, index) in node.children"
      :node="child"
      :offset="index"
      :key="node.id + index"
    >
    </node>
    <!-- </ul> -->
  </div>
</template>

<script>
export default {
  name: "node",
  props: {
    node: {
      type: Object,
      required: true,
    },
    offset: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  computed: {
    classObject: function () {
      var cl = "is-offset-" + this.offset;
      return {
        cl,
      };
    },
  },
};
</script>

<style scoped>
.ml-20 {
  max-height: 20rem;
  overflow-y: auto;
}
</style>