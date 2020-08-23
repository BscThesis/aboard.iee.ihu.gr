<template>
  <h1 class="title has-text-centered">{{ title }}</h1>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    }
  }
};
</script>

<style>
</style>