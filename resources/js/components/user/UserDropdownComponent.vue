<template>
  <div
    class="navbar-item has-dropdown"
    v-bind:class="{ 'is-active': openUserDropdown }"
    v-on-clickaway="away"
  >
    <a
      class="navbar-link is-arrowless is-hidden-touch"
      v-on:click="openUserDropdown = !openUserDropdown"
    >
      <span class="icon">
        <i class="fas fa-user fa-lg"></i>
      </span>
      <span class="icon">
        <i v-if="openUserDropdown" class="fas fa-chevron-up"></i>
        <i v-else class="fas fa-chevron-down"></i>
      </span>
    </a>

    <div class="navbar-dropdown is-radiusless is-right">
      <div class="navbar-item">
        <p class="is-size-6 has-text-weight-bold is-unselectable">{{ user.name }}</p>
      </div>
      <div class="navbar-item">
        <p class="is-size-6">{{ user.email }}</p>
      </div>
      <hr class="navbar-divider" />
      <a href="/user/preferences" class="navbar-item">
        <span class="is-size-6">User preferences</span>
      </a>
      <hr class="navbar-divider" />
      <a class="navbar-item" @click="logout()">
        <span class="is-size-6">Logout</span>
      </a>
    </div>
  </div>
</template>

<script>
import { mixin as clickaway } from "vue-clickaway";

export default {
  mixins: [clickaway],
  data: function() {
    return {
      openUserDropdown: false,
      user: {}
    };
  },
  created: function() {
    let vm = this;
    if (localStorage.getItem("user_info")) {
      vm.user = JSON.parse(localStorage.getItem("user_info"));
    }
  },
  methods: {
    away: function() {
      this.openUserDropdown = false;
    },
    logout: function() {
      axios
        .get("/api/auth/logout")
        .then(response => {
          localStorage.removeItem("token");
          localStorage.removeItem("user_info");
          delete axios.defaults.headers.common["Authorization"];
          window.location.href = "/login";
        })
        .catch(error => {
          localStorage.removeItem("token");
          localStorage.removeItem("user_info");
          delete axios.defaults.headers.common["Authorization"];
          window.location.href = "/login";
        });
    }
  }
};
</script>

<style>
</style>