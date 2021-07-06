<template>
  <div>
    <div
      class="modal"
      v-if="modalActive"
      v-bind:class="{ 'is-active': modalActive }"
    >
      <div
        class="modal-background"
        @click="closeModal()"
        @keyup.esc="closeModal()"
      ></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title has-text-centered" v-if="tag.id">
            Επεξεργασία ετικέτας
          </p>
          <p class="modal-card-title has-text-centered" v-else>
            Προσθήκη ετικέτας
          </p>
          <button
            class="delete"
            aria-label="close"
            @click="closeModal()"
          ></button>
        </header>
        <section class="modal-card-body">
          <errors-component
            v-if="errors.length"
            :errors="errors"
          ></errors-component>

          <!-- Tag Title -->
          <div class="field">
            <label class="label">Όνομα επικέτας</label>
            <div class="control has-icons-left">
              <input
                class="input"
                type="text"
                placeholder="Όνομα επικέτας"
                v-model="tag.title"
                required
              />
              <span class="icon is-small is-left">
                <i class="fas fa-tag"></i>
              </span>
            </div>
          </div>

          <!-- Tag Public -->
          <div class="field is-horizontal">
            <div class="field-label">
              <label class="label">Δημόσιο:</label>
            </div>
            <div class="field-body">
              <div class="field is-narrow">
                <div class="control">
                  <label class="checkbox">
                    <input type="checkbox" v-model="tag.is_public" />
                    Yes
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Parent Tag -->
          <div class="field">
            <label class="label">Γονική ετικέτα</label>
            <div class="control">
              <div class="select">
                <select v-model="tag.parent_id">
                  <option
                    v-for="_tag in tags"
                    v-if="_tag.id != tag.id"
                    v-bind:value="_tag.id"
                  >
                    {{ _tag.title }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </section>
        <footer class="modal-card-foot">
          <button class="button is-success" @click="saveTag()">
            Αποθήκευση
          </button>
          <button class="button" @click="closeModal()">Ακύρωση</button>
        </footer>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from "bulma-toast";
import { bus } from "../../app";

export default {
  data: function () {
    return {
      // single tag for insert/edit
      tag: {
        id: "",
        title: "",
        is_public: false,
        parent_id: null,
      },
      modalActive: false,
      edit: false,
      // errors
      errors: [],
      // all tags
      tags: {},
    };
  },
  mounted: function () {
    bus.$on("openCreateEditModal", (data) => {
      document.getElementsByTagName("html")[0].classList.add("is-clipped");
      this.errors = [];
      if (data.edit == true) {
        this.edit = data.edit;
        this.tag = data.tag;
      } else {
        this.tag = {
          id: "",
          title: "",
          is_public: false,
          parent_id: null,
        };
        this.edit = false;
      }
      this.tags = data.tags;
      this.modalActive = true;
    });
  },
  methods: {
    getSingleTag(id) {
      let vm = this;
      axios
        .get("/api/tags/" + vm.id)
        .then(function (response) {
          vm.tag = response.data.data;
        })
        .catch(function (error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
          });
          console.log(error);
        });
    },
    saveTag() {
      let vm = this;
      vm.errors = [];

      if (!vm.tag.title) {
        vm.errors.push("Το όνομα της ανακοίνωσης δεν μπορεί να είναι κενό");
      }

      if (vm.errors.length == 0) {
        if (vm.edit == false) {
          axios
            .post("/api/tags", {
              title: this.tag.title,
              parent_id: this.tag.parent_id,
              is_public: this.tag.is_public,
            })
            .then(function (response) {
              bus.$emit("objectCreated", true);
              vm.modalActive = false;
              document
                .getElementsByTagName("html")[0]
                .classList.remove("is-clipped");
              toast({
                message: "Αποθηκεύτηκε επιτυχώς",
                type: "is-success",
                position: "bottom-right",
              });
            })
            .catch(function (error) {
              toast({
                message: "Συνέβη κάποιο σφάλμα",
                type: "is-danger",
                position: "bottom-right",
              });
              console.log(error.response.data);
            });
        } else {
          axios
            .put("/api/tags/" + vm.tag.id, {
              title: this.tag.title,
              parent_id: this.tag.parent_id,
              is_public: this.tag.is_public,
            })
            .then(function (response) {
              bus.$emit("objectCreated", true);
              vm.modalActive = false;
              document
                .getElementsByTagName("html")[0]
                .classList.remove("is-clipped");
              toast({
                message: "Αποθηκεύτηκε επιτυχώς",
                type: "is-success",
                position: "bottom-right",
              });
            })
            .catch(function (error) {
              toast({
                message: "Συνέβη κάποιο σφάλμα",
                type: "is-danger",
                position: "bottom-right",
              });
              console.log(error);
            });
        }
      }
    },
    closeModal: function () {
      let vm = this;
      vm.modalActive = false;
      document.getElementsByTagName("html")[0].classList.remove("is-clipped");
      document.getElementsByTagName("html")[0].classList.remove("is-clipped");
    },
  },
};
</script>

<style>
</style>