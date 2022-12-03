<template>
  <div class="block">
    <div
      class="columns is-multiline is-mobile doc-item"
      v-bind:class="classObject"
    >
      <div class="column is-full is-clickable" @click="isOpen = !isOpen">
        <!-- Heading -->
        <div class="columns is-mobile">
          <div class="column is-narrow">
            <span class="icon has-text-white">
              <i
                v-if="isOpen == false"
                class="fas fa-chevron-right"
                aria-hidden="true"
              ></i>
              <i v-else class="fas fa-chevron-down" aria-hidden="true"></i>
            </span>
          </div>
          <div class="column is-narrow">
            <span
              class="tag is-light is-uppercase"
              v-bind:class="tagClassObject"
              >{{ requestType }}</span
            >
          </div>
          <div class="column">
            <p class="subtitle is-5 has-text-white">{{ title }}</p>
          </div>
          <div v-if="auth" class="column is-narrow">
            <span class="icon has-text-white" title="Authentication required">
              <i class="fas fa-lock"></i>
            </span>
          </div>
          <div v-if="admin" class="column is-narrow">
            <span class="icon has-text-white" title="Admin only">
              <i class="fas fa-user-shield"></i>
            </span>
          </div>
          <div v-if="!auth && !admin" class="column is-narrow">
            <span class="icon has-text-white" title="Authentication optional">
              <i class="fas fa-unlock"></i>
            </span>
          </div>
        </div>
      </div>
      <div
        class="column is-full has-background-white"
        v-bind:class="{ 'is-hidden': !isOpen }"
      >
        <!-- Content -->
        <article class="media">
          <div class="media-content">
            <div class="content">
              <h4 class="title is-4">{{ description }}</h4>
              <p v-if="request" class="subtitle is-6">Example curl request:</p>
              <pre v-if="request">{{ request }}</pre>
              <p v-if="response" class="subtitle is-6">Example response:</p>
              <pre v-if="response">{{ response | pretty }}</pre>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    requestType: {
      type: String,
      required: false,
    },
    description: {
      type: String,
      required: true,
    },
    auth: {
      type: Boolean,
      required: false,
    },
    admin: {
      type: Boolean,
      required: false,
    },
    response: {
      type: String,
      required: false,
    },
    request: {
      type: String,
      required: false,
    },
  },
  data: function () {
    return {
      isOpen: false,
    };
  },
  computed: {
    classObject: function () {
      return {
        "has-background-link": this.requestType === "GET",
        "has-background-danger": this.requestType === "DELETE",
        "has-background-success": this.requestType === "POST",
        "has-background-primary": this.requestType === "PUT",
      };
    },
    tagClassObject: function () {
      return {
        "is-link": this.requestType === "GET",
        "is-danger": this.requestType === "DELETE",
        "is-success": this.requestType === "POST",
        "is-primary": this.requestType === "PUT",
      };
    },
  },
  filters: {
    pretty: function (value) {
      return JSON.stringify(JSON.parse(value), null, 2);
    },
  },
};
</script>

<style scoped>
.tag {
  min-width: 4rem;
}
.doc-item {
  border-radius: 8px;
}

.content pre {
  white-space: pre-wrap;
}
</style>