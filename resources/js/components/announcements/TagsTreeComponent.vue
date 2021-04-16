<template>
  <nav class="panel is-shadowless">
    <!-- Search START -->
    <!-- <div class="panel-block">
      <p class="control has-icons-left">
        <input
          class="input is-small"
          type="text"
          placeholder="Search"
          v-model="searchTerm"
        />
        <span class="icon is-left">
          <i class="fas fa-search" aria-hidden="true"></i>
        </span>
      </p>
    </div> -->
    <!-- Search END -->

    <node-tree :node="treeData"></node-tree>
  </nav>
</template>

<script>
export default {
  props: {
    treeData: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {
      searchTerm: "",
      open: false,
      clicked: false,
    };
  },
};
</script>

<style>
</style>