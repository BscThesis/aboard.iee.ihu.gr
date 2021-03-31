<template>
  <div class="block">
    <article class="message is-danger">
      <div class="message-body">
        <span>Παρακαλώ διορθώστε τα παρακάτω σφάλματα:</span>
        <div class="content">
          <ul>
            <li
              v-for="(error,index) in errors"
              v-bind:key="`error-${index}`"
              v-bind:error="error"
            >{{ error }}</li>
          </ul>
        </div>
      </div>
    </article>
  </div>
</template>

<script>
export default {
  props: {
    errors: {
      type: Array,
      required: true
    }
  }
};
</script>
