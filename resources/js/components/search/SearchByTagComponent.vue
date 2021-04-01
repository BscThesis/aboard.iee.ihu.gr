<template>
  <div class="block">
    <div class="block">
      <!-- Breadcrumb -->
      <breadcrumb-component
        v-if="tag.title"
        v-bind:title="tag.title"
        v-bind:tag="true"
      ></breadcrumb-component>

      <!-- Search results title -->
      <page-title title="Αποτελέσματα αναζήτησης"></page-title>

      <!-- Pagination -->
      <pagination-component></pagination-component>

      <!-- Announcements 1 -->
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
  </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data: function () {
    return {
      tag: {
        id: "",
        title: "",
        parent_id: "",
        is_public: "",
      },
      announcements: {},
    };
  },
  created: function () {
    this.getSingleTag();
    this.getResults();
    bus.$on("next", (data) => {
      this.getResults(data);
    });
    bus.$on("prev", (data) => {
      this.getResults(data);
    });
  },
  methods: {
    getSingleTag() {
      let vm = this;
      axios
        .get("/api/tags/" + this.id)
        .then(function (response) {
          vm.tag = response.data.data;
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
    getResults(page_url = "/api/search/tag/" + this.id) {
      let vm = this;
      page_url = page_url;

      axios
        .get(page_url)
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
            animate: { in: "fadeIn", out: "fadeOut" },
          });
          console.log(error);
        });
    },
  },
};
</script>