<template>
  <div class="column is-full" v-if="attachments.length > 0">
    <div class="columns is-mobile">
      <div class="column is-narrow">
        <span class="has-text-weight-bold">Συνημμένα:</span>
      </div>
      <div class="column">
        <div class="field is-grouped is-grouped-multiline">
          <div
            class="control"
            v-for="attachment in attachments"
            v-bind:key="attachment.id"
          >
            <div
              class="tags"
              v-bind:class="{
                'has-addons': attachment.filename.endsWith('.pdf'),
              }"
            >
              <a
                class="tag is-info is-light"
                v-bind:href="`/announcements/${attachment.announcement_id}/attachments/${attachment.id}`"
                target="_blank"
              >
                {{ attachment.filename }}
                <span
                  v-if="attachment.filename.endsWith('.pdf')"
                  title="Θέαση"
                  class="icon"
                >
                  <i class="far fa-eye"></i>
                </span>
              </a>
              <!-- <a
                class="tag is-info is-light"
                v-bind:href="`/announcements/${attachment.announcement_id}/attachments/${attachment.id}`"
                target="_blank"
                v-if="attachment.filename.endsWith('.pdf')"
                title="Θέαση"
              >
                <span class="icon"> <i class="far fa-eye"></i> </span>
              </a> -->
            </div>
          </div>
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
  },
};
</script>