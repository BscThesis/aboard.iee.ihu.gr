<template>
  <div class="column content has-text-justified is-marginless is-full" v-html="body"></div>
</template>

<script>
export default {
  props: {
    body: {
      type: String,
      required: true
    }
  }
};
</script>

<style scoped>
.content-mini {
  max-height: 6rem;
  overflow: hidden;
}
</style>

