<template>
  <div class="box">
    <article class="media">
      <div class="media-content is-clipped">
        <div class="content">
          <div class="columns is-multiline is-mobile">
            <!-- title -->
            <single-announcement-title
              v-if="displayEnglish == false"
              v-bind:title="announcement.title"
              v-bind:id="announcement.id"
            ></single-announcement-title>

            <single-announcement-title
              v-if="displayEnglish == true"
              v-bind:title="announcement.eng_title"
              v-bind:id="announcement.id"
            ></single-announcement-title>

            <!-- English -->
            <div class="column is-narrow" v-if="announcement.has_eng">
              <span
                class="icon is-clickable"
                @click="displayEnglish = !displayEnglish"
                title="This announcement is available in English"
              >
                <i class="fas fa-exchange-alt"></i>
              </span>
            </div>

            <!-- Dropdown -->
            <div class="column is-narrow">
              <span class="is-pulled-right">
                <span class="icon is-clickable" @click="open = !open">
                  <i v-if="open == false" class="fas fa-chevron-right"></i>
                  <i v-if="open == true" class="fas fa-chevron-down"></i>
                </span>
              </span>
            </div>

            <!-- Announcement body -->
            <full-announcement-body
              v-bind:class="{ 'has-text-hidden': !open }"
              v-if="announcement.body && displayEnglish == false"
              v-bind:body="announcement.body"
            ></full-announcement-body>

            <full-announcement-body
              v-bind:class="{ 'has-text-hidden': !open }"
              v-if="announcement.eng_body && displayEnglish == true"
              v-bind:body="announcement.eng_body"
            ></full-announcement-body>

            <!-- Tags -->
            <tags v-if="announcement.tags" v-bind:tags="announcement.tags"></tags>

            <!-- Attachments -->
            <attachments
              v-if="announcement.attachments"
              v-bind:attachments="announcement.attachments"
            ></attachments>
          </div>
        </div>
        <!-- Last update -->
        <last-update
          v-if="announcement.updated_at"
          v-bind:updated_at="announcement.updated_at"
          v-bind:author="announcement.author"
        ></last-update>
      </div>
    </article>
  </div>
</template>

<script>
export default {
  props: {
    announcement: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      displayEnglish: false,
      open: false
    };
  }
};
</script>

