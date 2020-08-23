<template>
  <div>
    <section class="section" id="addTagButton">
      <a class="button is-link is-capitalized" @click="openCreateEditModal()">Προσθήκη επικέτας</a>
    </section>

    <!-- Tags Table -->
    <table v-if="tags.length >= 0" class="table is-fullwidth is-bordered is-hoverable">
      <thead>
        <tr>
          <!-- <th class="has-text-centered">ID</th> -->
          <th class="has-text-centered sixty-percent">Όνομα</th>
          <!-- <th class="has-text-centered">Γονική ετικέτα</th> -->
          <!-- <th class="has-text-centered">Δημόσιο</th> -->
          <th class="has-text-centered forty-percent">Διαχείριση</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="tag in tags" v-bind:key="tag.id">
          <!-- <td class="has-text-centered">{{ tag.id }}</td> -->
          <td class="has-text-centered" v-bind:title="tag.title">{{ tag.title }}</td>
          <!-- <td class="has-text-centered">{{ tag.parent_id }}</td> -->
          <!-- <td class="has-text-centered">{{ tag.is_public }}</td> -->
          <td class="has-text-centered">
            <div class="buttons is-centered">
              <button
                class="button is-success is-light"
                title="Επεξεργασία"
                v-on:click="openCreateEditModal(tag, true)"
              >
                <span class="icon is-small">
                  <i class="fas fa-edit"></i>
                </span>
              </button>
              <button
                class="button is-danger is-light"
                title="Διαγραφή"
                v-on:click="openDeleteModal(tag)"
              >
                <span class="icon is-small">
                  <i class="fas fa-trash"></i>
                </span>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Loader -->
    <loader-component v-else></loader-component>

    <!-- Add/Edit Modal -->
    <add-tag-component></add-tag-component>

    <!-- Delete Modal -->
    <delete-modal-component></delete-modal-component>
  </div>
</template>

<script>
import { toast } from "bulma-toast";
import { bus } from "../../app";

export default {
  data: function() {
    return {
      tags: {}
    };
  },
  created: function() {
    this.getAllTags();
    bus.$on("objectCreated", data => {
      if (data) {
        this.getAllTags();
      }
    });
    bus.$on("objectRemoved", data => {
      if (data) {
        this.getAllTags();
      }
    });
  },
  methods: {
    getAllTags() {
      let vm = this;
      axios
        .get("/api/tags")
        .then(function(response) {
          vm.tags = response.data.data;
        })
        .catch(function(error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" }
          });
          console.log(error);
        });
    },
    openCreateEditModal(tag = null, edit = false) {
      let data = {
        edit: edit,
        tags: this.tags,
        tag: tag
      };
      bus.$emit("openCreateEditModal", data);
    },
    openDeleteModal(tag) {
      let vm = this;
      let forDeletion = {
        type: "tag",
        data: tag
      };
      bus.$emit("openModal", forDeletion);
    }
  }
};
</script>

<style scoped>
table {
  table-layout: fixed;
}

td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sixty-percent {
  width: 60%;
}

.forty-percent {
  width: 40%;
}
</style>
