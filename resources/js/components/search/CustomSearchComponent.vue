<template>
  <div class="block">
    <!-- Title -->
    <page-title title="Αποτελέσματα αναζήτησης"></page-title>

    <!-- Search/Pinned Box -->
    <search-component></search-component>

    <!-- Pagination 1 -->
    <pagination-component></pagination-component>

    <!-- Announcements -->
    <div class="block is-clipped">
      <!-- Loader -->
      <loader-component></loader-component>

      <!-- Announcement loop -->
      <single-announcement-component
        v-for="announcement in announcements"
        v-bind:key="announcement.id"
        v-bind:announcement="announcement"
      ></single-announcement-component>

      <!-- Pagination 2 -->
      <pagination-component></pagination-component>
    </div>
  </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
  props: {
    params: {
      type: Array,
      required: true,
    },
  },
  data: function () {
    return {
      announcements: {},
    };
  },
  created: function () {
    this.getResults();
    bus.$on("next", (data) => {
      this.getResults(data);
    });
    bus.$on("prev", (data) => {
      this.getResults(data);
    });
  },
  methods: {
    getResults(page_url = "/api/search") {
      let vm = this;
      page_url = page_url;

      axios
        .get(page_url, {
          params: {
            q: JSON.stringify(vm.params),
          },
        })
        .then(function (response) {
          vm.announcements = response.data.data;
          let paginate = {
            links: response.data.links,
            meta: response.data.meta,
          };
          bus.$emit("paginationObject", paginate);
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
    },
  },
};
</script>

<style>
</style>