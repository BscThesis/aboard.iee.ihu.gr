<template>
  <div class="column is-4">
    <article class="message is-dark has-background-white">
      <div class="message-body">
        <div class="columns is-multiline is-mobile">
          <div class="column is-full">
            <a
              class="is-not-decorated"
              v-bind:href="'/announcements/' + event.id"
            >
              <p class="is-size-5 has-text-centered">{{ event.title }}</p>
            </a>
          </div>
          <div class="column is-full">
            <div class="columns is-mobile is-multiline">
              <div class="column is-full">
                <p class="help is-italic is-gray">Τοποθεσία εκδήλωσης</p>
                <span class="icon">
                  <i class="fas fa-map-marker-alt"></i>
                </span>
                <a
                  target="_blank"
                  v-if="event.gmaps"
                  v-bind:href="
                    'https://www.google.com/maps/search/?api=1&query=' +
                    encodeURL(event.event_location)
                  "
                  >{{ event.event_location }}</a
                >
                <span v-else>{{ event.event_location }}</span>
              </div>
              <div class="column is-full">
                <p class="help is-italic is-gray">Ώρα έναρξης</p>
                <span class="icon">
                  <i class="fas fa-clock"></i>
                </span>
                <span v-if="event.event_start_time">{{
                  event.event_start_time
                }}</span>
                <span v-else>-</span>
              </div>
              <div class="column is-full">
                <p class="help is-italic is-gray">Ώρα λήξης</p>
                <span class="icon">
                  <i class="fas fa-clock"></i>
                </span>
                <span v-if="event.event_end_time">{{
                  event.event_end_time
                }}</span>
                <span v-else>-</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </article>
  </div>
</template>

<script>
export default {
  props: {
    event: {
      type: Object,
      required: true,
    },
  },
  methods: {
    encodeURL(value) {
      return encodeURI(value);
    },
  },
};
</script>