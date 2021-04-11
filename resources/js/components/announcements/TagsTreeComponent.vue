<template>
  <nav class="panel is-shadowless">
    <!-- Search START -->
    <div class="panel-block">
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
    </div>
    <!-- Search END -->

    <div class="panel-block tags-panel-block">
      <div class="columns is-mobile">
        <div class="column is-offset-0 is-narrow">
          <tag-caret></tag-caret>
        </div>
        <div class="column is-narrow">
          <tag-click></tag-click>
        </div>
        <div class="column is-narrow">
          <span class="is-unselectable">1o etos</span>
        </div>
      </div>
    </div>
    <!--  -->
    <!-- <div class="panel-block tags-panel-block">
      <div class="columns is-mobile">
        <div class="column is-offset-1 is-narrow">
          <span class="icon is-clickable">
            <i class="fas fa-caret-down"></i>
          </span>
        </div>
        <div class="column is-narrow">
          <span class="icon is-clickable">
            <i class="far fa-check-square"></i>
          </span>
        </div>
        <div class="column is-narrow">
          <span class="is-unselectable">2o etos</span>
        </div>
      </div>
    </div>

    <div class="panel-block tags-panel-block">
      <div class="columns is-mobile">
        <div class="column is-offset-2 is-narrow">
          <span class="icon is-clickable">
            <i class="fas fa-caret-right"></i>
          </span>
        </div>
        <div class="column is-narrow">
          <span class="icon is-clickable">
            <i class="far fa-square"></i>
          </span>
        </div>
        <div class="column is-narrow">
          <span class="is-unselectable">3o etos</span>
        </div>
      </div>
    </div>

    <div class="panel-block tags-panel-block">
      <div class="columns is-mobile">
        <div class="column is-offset-0 is-narrow">
          <span class="icon is-clickable">
            <i class="fas fa-caret-right"></i>
          </span>
        </div>
        <div class="column is-narrow">
          <span class="icon is-clickable">
            <i class="far fa-square"></i>
          </span>
        </div>
        <div class="column is-narrow">
          <span class="is-unselectable">4o etos</span>
        </div>
      </div>
    </div> -->
    <!--  -->
  </nav>
</template>

<script>
export default {
  props: {
    treeData: {
      type: Array,
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