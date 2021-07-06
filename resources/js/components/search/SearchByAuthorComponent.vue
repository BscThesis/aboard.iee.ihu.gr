<template>
  <div class="block">
    <div class="block">
      <!-- Breadcrumb -->
      <breadcrumb-component
        v-if="author.name"
        v-bind:title="author.name"
        v-bind:author="true"
      ></breadcrumb-component>

      <!-- Search results title -->
      <page-title title="Αποτελέσματα αναζήτησης"></page-title>

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
      author: {
        id: "",
        name: "",
      },
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
    getResults(page_url = "/api/search/author/" + this.id) {
      let vm = this;
      page_url = page_url;

      axios
        .get(page_url)
        .then(function (response) {
          if (response.data.data.length > 0) {
            vm.announcements = response.data.data;
            vm.author = response.data.data[0].author;
            let paginate = {
              links: response.data.links,
              meta: response.data.meta,
            };
            bus.$emit("paginationObject", paginate);
          } else {
            toast({
              message: "Δε βρέθηκε",
              type: "is-danger",
              position: "bottom-right",
            });
          }

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