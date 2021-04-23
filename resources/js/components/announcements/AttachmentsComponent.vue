<template>
  <div class="column is-full" v-if="attachments.length > 0">
    <div class="columns is-mobile">
      <div class="column is-narrow">
        <span class="has-text-weight-bold">Συνημμένα:</span>
      </div>
      <div class="column">
        <div class="tags">
          <!-- <a
            class="tag is-info is-light"
            v-for="attachment in attachments"
            v-bind:key="attachment.id"
            v-bind:href="
              'data:' + attachment.mime_type + ';base64,' + attachment.content
            "
            v-bind:download="attachment.filename"
          >
            {{ attachment.filename }}
          </a> -->
          <a
            class="tag is-info is-light"
            v-for="attachment in attachments"
            v-bind:key="attachment.id"
            v-bind:href="'/api/announcements/'+ announcement_id '/attachments/' + attachment.id"
          >
            {{ attachment.filename }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    attachments: {
      type: Array,
      required: true,
    },
    announcement_id: {
      type: Number,
      required: true,
    },
  },
};
</script>