<template>
  <section class="hero is-medium">
    <div class="hero-body">
      <div class="container has-text-centered">
        <div class="columns is-multiline">
          <div class="column is-full">
            <div class="columns is-centered">
              <div class="column is-narrow">
                <span class="icon is-medium has-text-danger">
                  <i class="fas fa-2x fa-exclamation-circle"></i>
                </span>
              </div>
              <div class="column is-narrow">
                <h1 class="title has-text-danger">Error {{ code }}</h1>
              </div>
            </div>
          </div>
          <div class="column is-full">
            <h2 class="subtitle">{{ message }}</h2>
          </div>
          <div class="column is-full">
            <a href="/announcements" class="button is-info is-light">
              <span class="icon">
                <i class="fas fa-home"></i>
              </span>
              <span>Αρχική</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  props: {
    code: {
      type: String,
      required: true,
    },
    message: {
      type: String,
      required: true,
    },
  },
};
</script>

