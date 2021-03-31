<template>
  <nav class="breadcrumb is-small is-right has-bullet-separator" aria-label="breadcrumbs">
    <ul>
      <li class="is-active">
        <a>{{ updated_at }}</a>
      </li>
      <li>
        <a :href="'/search/author/' + author.id">{{ author.name }}</a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    updated_at: {
      type: String,
      required: true
    },
    author: {
      type: Object,
      required: true
    }
  }
};
</script>