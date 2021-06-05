<template>
  <!-- Search/Pinned Box -->
  <div class="block">
    <div class="box">
      <div class="columns">
        <div class="column is-two-thirds">
          <!-- Search box -->
          <div class="block columns">
            <div class="column">
              <div class="field has-addons">
                <div class="control is-expanded">
                  <input
                    class="input"
                    type="text"
                    placeholder="Προσθέστε όρο αναζήτησης"
                    v-on:keyup.enter="addToSearchList()"
                    v-model="search"
                  />
                  <p class="help is-gray is-italic">
                    Προσθέστε όρους και πατήστε Αναζήτηση
                  </p>
                </div>
                <div class="control">
                  <a
                    class="button"
                    @click="doSearch()"
                    :disabled="searchInput.length == 0"
                  >
                    <span class="icon">
                      <i class="fas fa-search"></i>
                    </span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- List of tags -->
          <div class="field is-grouped is-grouped-multiline">
            <div
              class="control"
              v-for="(item, index) in searchInput"
              v-bind:key="index"
            >
              <div class="tags">
                <span class="tag is-rounded is-light">
                  <span>{{ item }}</span>
                  <button
                    class="delete is-small"
                    v-on:click="removeFromSearchList(index)"
                  ></button>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Pinned announcements -->
        <div class="column is-one-third">
          <table class="table is-narrow is-hoverable is-fullwidth">
            <thead>
              <tr>
                <th class="has-text-centered">
                  <span class="icon">
                    <i class="fas fa-thumbtack"></i>
                  </span>
                  Σημαντικές ανακοινώσεις
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="pin in pinned"
                v-if="pinned.length > 0"
                v-bind:key="pin.id"
              >
                <td class="has-text-centered">
                  <a v-bind:href="'/announcements/' + pin.id">{{
                    pin.title
                  }}</a>
                </td>
              </tr>
              <tr v-if="pinned.length == 0">
                <td class="has-text-centered is-unselectable">
                  Καμία ανακοίνωση
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from "bulma-toast";

export default {
  data() {
    return {
      searchInput: [],
      search: "",
      pinned: [],
    };
  },
  created: function () {
    this.getPinnedAnnouncements();
  },
  methods: {
    getPinnedAnnouncements(page_url = "/api/pinned") {
      let vm = this;
      axios
        .get(page_url)
        .then(function (response) {
          vm.pinned = response.data;
        })
        .catch(function (error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" },
          });
          console.log(error);
        });
    },
    addToSearchList: function () {
      let vm = this;
      if (!vm.searchInput.includes(vm.search) && vm.search.trim() !== "") {
        vm.searchInput.push(vm.search);
      } else if (vm.search.trim() == "" && vm.searchInput.length > 0) {
        this.doSearch();
      }
      vm.search = "";
    },
    removeFromSearchList: function (index) {
      let vm = this;
      vm.$delete(vm.searchInput, index);
    },
    doSearch() {
      let vm = this;
      if (vm.searchInput.length > 0) {
        window.location.replace("/search/q=" + JSON.stringify(vm.searchInput));
      }
    },
  },
};
</script>

<style>
</style>