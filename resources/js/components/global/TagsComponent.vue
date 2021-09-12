<template>
  <div class="block">
    <!-- Page Title -->
    <page-title title="Ετικέτες"></page-title>

    <!-- Loader -->
    <loader-component></loader-component>

    <!-- Content -->

    <div class="block">
      <div class="box">
        <p class="control has-icons-left has-icons-right">
          <input
            class="input is-rounded"
            type="text"
            placeholder="Αναζήτηση..."
            v-model="search"
          />
          <span class="icon is-left">
            <i class="fas fa-search" aria-hidden="true"></i>
          </span>
          <span v-show="search" class="icon is-small is-right">
            <a class="delete" @click="search = ''"></a>
          </span>
        </p>
        <p class="help is-gray is-italic is-unselectable">
          Προσθέστε όρους και πατήστε Αναζήτηση
        </p>
      </div>
    </div>

    <div class="block">
      <div class="columns is-multiline is-vcentered">
        <div
          class="column is-half"
          v-for="tag in filteredTags"
          v-bind:key="tag.id"
        >
          <div class="box ihu-tag" @click="searchByTag(tag.id)">
            <div class="columns is-mobile is-vcentered">
              <div class="column is-10">
                <h1 class="title is-4 is-unselectable">{{ tag.title }}</h1>
              </div>
              <div class="column">
                <span class="icon">
                  <i class="fas fa-lg fa-chevron-right"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
  data: function () {
    return {
      tags: [],
      search: "",
    };
  },
  created: function () {
    this.getTags();
  },
  methods: {
    getTags: function () {
      let vm = this;
      axios
        .get("/api/tags")
        .then(function (response) {
          vm.tags = response.data.data;
          bus.$emit("loadingFinished", true);
        })
        .catch(function (error) {
          bus.$emit("loadingFinished", true);
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
          });
          console.log(error);
        });
      console.log(vm.tags);
    },
    searchByTag: function (id) {
      window.location.href = "/search/tag/" + id;
    },
  },
  computed: {
    filteredTags: function () {
      let vm = this;
      if (vm.search == "") {
        return vm.tags.filter(function (el) {
          return el.parent_id != null;
        });
        // return vm.tags;
      } else {
        return vm.tags.filter(function (el) {
          return (
            el.title.toLowerCase().includes(vm.search.toLowerCase()) &&
            el.parent_id != null
          );
        });
      }
    },
  },
};
</script>

<style scoped>
.ihu-tag:hover {
  cursor: pointer;
  color: #1a6dae;
}
</style>