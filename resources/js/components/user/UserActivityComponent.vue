<template>
  <div
    class="navbar-item has-dropdown is-hidden-touch"
    v-bind:class="{ 'is-active': openActivityDropdown }"
    v-on-clickaway="away"
  >
    <a
      class="navbar-link is-arrowless is-hidden-touch"
      v-on:click="openActivityDropdown = !openActivityDropdown"
    >
      <span class="icon">
        <i class="fas fa-bell fa-lg"></i>
      </span>
      <span
        v-if="newNotifications > 0"
        class="tag is-danger is-rounded is-light"
        >{{ newNotifications }}</span
      >
      <span class="icon">
        <i v-if="openActivityDropdown" class="fas fa-chevron-up"></i>
        <i v-else class="fas fa-chevron-down"></i>
      </span>
    </a>

    <div class="navbar-dropdown is-radiusless is-paddingless is-right">
      <div
        class="notification is-uppercase is-radiusless is-marginless has-text-weight-bold is-unselectable"
      >
        Activity (Last 10)
      </div>
      <section class="activity-list">
        <div class="columns is-mobile is-marginless is-multiline">
          <div
            v-for="notification in notifications"
            v-bind:key="notification.id"
            class="column is-marginless is-full activity-list-item"
          >
            <div class="columns is-marginless">
              <div class="column is-narrow">
                <span class="icon has-text-link">
                  <i
                    class="fas fa-lg fa-info"
                    :title="notification.created_at"
                  ></i>
                </span>
              </div>
              <div class="column">
                <p class="is-size-6" v-if="notification.data.title">
                  <a v-bind:href="'/announcements/' + notification.data.id">{{
                    notification.data.title
                  }}</a>
                </p>
                <p class="is-size-6" v-else>{{ notification.data.type }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <footer>
        <a
          class="button is-radiusless is-danger is-fullwidth is-uppercase"
          href="/user/preferences"
          >View all</a
        >
      </footer>
    </div>
  </div>
</template>

<script>
import { mixin as clickaway } from "vue-clickaway";

export default {
  mixins: [clickaway],
  data: function () {
    return {
      openActivityDropdown: false,
      notifications: [],
    };
  },
  created: function () {
    this.fetchNotifications();
  },
  methods: {
    away: function () {
      this.openActivityDropdown = false;
    },
    fetchNotifications: function () {
      let vm = this;
      axios
        .get("/api/auth/user/notifications")
        .then((response) => {
          vm.notifications = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    readNotifications: function () {
      let vm = this;
      axios
        .get("/api/auth/user/notifications/read")
        .then((response) => {
          vm.notifications = response.data.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  watch: {
    openActivityDropdown: function (newVal) {
      if (newVal) {
        this.fetchNotifications();
        this.readNotifications();
      }
    },
  },
  computed: {
    newNotifications: function () {
      let vm = this;
      let number = 0;
      vm.notifications.filter(function (el) {
        if (!el.read_at) {
          number++;
        }
      });
      return number;
    },
  },
};
</script>

<style scoped>
.navbar-dropdown {
  width: 455px;
}
.activity-list {
  height: 300px;
  overflow-y: auto;
  overflow-x: hidden;
}
.activity-list-item {
  border-bottom: 1px solid #dbdbdb;
  padding: 12px 10px;
}
</style>
