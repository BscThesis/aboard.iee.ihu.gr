<template>
  <div class="block">
    <!-- Title -->
    <page-title title="Ανακοινώσεις"></page-title>

    <!-- Links -->
    <front-page-links></front-page-links>

    <!-- Search/Pinned Box -->
    <search-component></search-component>

    <!-- Pagination -->
    <pagination-component></pagination-component>

    <!-- Announcements -->
    <div class="block">
      <!-- Loader -->
      <loader-component></loader-component>

      <!-- Announcement loop -->
      <single-announcement-component
        v-for="announcement in announcements"
        v-bind:key="announcement.id"
        v-bind:announcement="announcement"
      ></single-announcement-component>
    </div>
  </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
  data: function() {
    return {
      announcements: {}
    };
  },
  mounted: function() {
    this.getAllAnnouncements();
    bus.$on("next", data => {
      this.getAllAnnouncements(data);
    });
    bus.$on("prev", data => {
      this.getAllAnnouncements(data);
    });
  },
  methods: {
    getAllAnnouncements(page_url = "/api/announcements") {
      let vm = this;
      page_url = page_url;
      axios
        .get(page_url)
        .then(function(response) {
          vm.announcements = response.data.data;
          let paginate = {
            links: response.data.links,
            meta: response.data.meta
          };
          bus.$emit("paginationObject", paginate);
          bus.$emit("loadingFinished", true);
        })
        .catch(function(error) {
          bus.$emit("loadingFinished", true);
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" }
          });
          console.log(error);
        });
    }
  }
};
</script>
