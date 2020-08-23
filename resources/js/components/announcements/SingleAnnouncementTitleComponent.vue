<template>
  <div class="column">
    <a v-bind:href="'/announcements/' + id" class="is-inline-block">
      <h4 class="title is-4 is-marginless">{{ title }}</h4>
    </a>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    id: {
      type: Number,
      required: true
    }
  }
};
</script>

