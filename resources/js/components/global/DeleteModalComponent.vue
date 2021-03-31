<template>
  <div>
    <div class="modal" v-if="removeModalActive" v-bind:class="{ 'is-active': removeModalActive }">
      <div class="modal-background" @click="closeModal()"></div>
      <div class="modal-content">
        <div class="card">
          <header class="card-header">
            <p
              v-if="forDeletion.type == 'tag'"
              class="card-header-title"
            >Πρόκειται να διαγράψετε μία επικέτα!</p>
            <p
              v-if="forDeletion.type == 'announcement'"
              class="card-header-title"
            >Πρόκειται να διαγράψετε μία ανακοίνωση!</p>
          </header>
          <div class="card-content">
            <div class="content">Να προχωρήσω;</div>
          </div>
          <footer class="card-footer">
            <a href="#" class="card-footer-item" @click="deleteObject()">Ναι</a>
            <a href="#" class="card-footer-item" @click="closeModal()">Όχι</a>
          </footer>
        </div>
      </div>
      <button class="modal-close is-large" aria-label="close" @click="closeModal()"></button>
    </div>
  </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
  created: function() {
    bus.$on("openModal", data => {
      document.getElementsByTagName("html")[0].classList.add("is-clipped");
      this.forDeletion = data;
      this.removeModalActive = !this.removeModalActive;
    });
  },
  data: function() {
    return {
      removeModalActive: false,
      forDeletion: {}
    };
  },
  methods: {
    deleteObject: function() {
      let vm = this;

      let url = null;
      if (vm.forDeletion.type == "tag") {
        url = "/api/tags/";
      } else if (vm.forDeletion.type == "announcement") {
        url = "/api/announcements/";
      }

      if (url) {
        axios
          .delete(url + vm.forDeletion.data.id)
          .then(function(response) {
            toast({
              message: "Διεγράφη επιτυχώς",
              type: "is-success",
              position: "bottom-right",
              animate: { in: "fadeIn", out: "fadeOut" }
            });
            document
              .getElementsByTagName("html")[0]
              .classList.remove("is-clipped");
            vm.removeModalActive = !vm.removeModalActive;
            bus.$emit("objectRemoved", true);
          })
          .catch(function(error) {
            toast({
              message: "Συνέβη κάποιο σφάλμα",
              type: "is-danger",
              position: "bottom-right",
              animate: { in: "fadeIn", out: "fadeOut" }
            });
            document
              .getElementsByTagName("html")[0]
              .classList.remove("is-clipped");
            vm.removeModalActive = !vm.removeModalActive;
            console.log(error);
          });
      }
    },
    closeModal: function() {
      let vm = this;
      document.getElementsByTagName("html")[0].classList.remove("is-clipped");
      vm.removeModalActive = false;
    }
  }
};
</script>

