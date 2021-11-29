<template>
  <a
    class="button is-success is-light"
    v-if="id"
    v-bind:href="'/announcements/' + id + '/edit'"
  >
    <span class="icon">
      <i class="fas fa-edit"></i>
    </span>
    <span class="is-capitalized">Επεξεργασία</span>
  </a>
</template>

<script>
export default {
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data: function () {
    return {};
  },
};
</script>