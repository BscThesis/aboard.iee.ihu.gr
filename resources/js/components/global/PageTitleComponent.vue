<template>
  <section class="section">
    <h1 class="title is-unselectable">{{ title }}</h1>
  </section>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
  },
  data() {
    return {};
  },
};
</script>
