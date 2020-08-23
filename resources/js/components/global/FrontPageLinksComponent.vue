<template>
  <div class="block" v-if="is_admin == true || is_author == true">
    <div class="buttons is-right">
      <!-- <a v-if="is_admin" class="button is-danger is-uppercase" href="/admin">Admin area</a> -->
      <!-- <admin-modal v-if="is_admin"></admin-modal> -->
      <button
        v-if="is_admin"
        class="button is-danger is-uppercase"
        @click="modalOpen=true"
      >Admin area</button>
      <a
        v-if="is_author"
        class="button is-dark is-capitalized"
        href="/announcements/create"
      >Προσθήκη ανακοίνωσης</a>
    </div>

    <div v-if="is_admin" class="modal" v-bind:class="{ 'is-active': modalOpen }">
      <div class="modal-background" @click="modalOpen=false">></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Διαχείριση ετικετών</p>
          <button class="delete" aria-label="close" @click="modalOpen=false"></button>
        </header>
        <section class="modal-card-body">
          <admin-component></admin-component>
        </section>
        <footer class="modal-card-foot">
          <button class="button" @click="modalOpen=false">Exit</button>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
import { bus } from "../../app";

export default {
  data: function() {
    return {
      is_author: false,
      is_admin: false,
      modalOpen: false
    };
  },
  watch: {
    modalOpen: function(value) {
      if (value) {
        document.getElementsByTagName("html")[0].classList.add("is-clipped");
      } else {
        document.getElementsByTagName("html")[0].classList.remove("is-clipped");
      }
    }
  },
  mounted: function() {
    bus.$on("authCheckFinished", () => {
      if (localStorage.getItem("user_info")) {
        let vm = this;
        vm.is_author = JSON.parse(localStorage.getItem("user_info")).is_author;
        vm.is_admin = JSON.parse(localStorage.getItem("user_info")).is_admin;
      }
    });
  }
};
</script>

<style scoped>
.modal-card-body {
  padding: 0px 20px;
}
</style>