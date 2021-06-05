<template>
  <div class="column is-full">
    <div class="columns">
      <div class="column">
        <div class="tags">
          <span
            class="tag is-dark is-clickable"
            v-for="tag in filteredTags"
            v-bind:key="tag.id"
            @click="tagSearch(tag.id)"
            >{{ tag.title }}</span
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    tags: {
      type: Array,
      required: true,
    },
  },
  methods: {
    tagSearch(id) {
      window.location.href = "/search/tag/" + id;
    },
  },
  computed: {
    filteredTags: function () {
      let vm = this;
      return vm.tags.filter(function (el) {
        return el.parent_id;
      });
    },
  },
};
</script>