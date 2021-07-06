<template>
  <div class="block">
    <!-- Page Title -->
    <page-title title="Εκδηλώσεις"></page-title>

    <!-- Content -->
    <div class="block">
      <!-- Pagination -->
      <pagination-component></pagination-component>

      <!-- Events -->
      <div class="columns is-vcentered is-multiline">
        <event-component
          v-for="event in events"
          v-bind:key="event.id"
          v-bind:event="event"
        ></event-component>
      </div>

      <!-- Loader -->
      <loader-component></loader-component>
    </div>
  </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
  data: function () {
    return {
      events: {},
    };
  },
  created: function () {
    this.getEvents();
    bus.$on("next", (data) => {
      this.getEvents(data);
    });
    bus.$on("prev", (data) => {
      this.getEvents(data);
    });
  },

  methods: {
    getEvents(page_url = "/api/events") {
      let vm = this;
      page_url = page_url;
      axios
        .get(page_url)
        .then(function (response) {
          vm.events = response.data.data;
          // manually create links and meta objects
          // and pass them to the pagination object
          let links = {
            next: response.data.next_page_url,
            prev: response.data.prev_page_url,
          };

          let meta = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
          };

          let paginate = {
            links: links,
            meta: meta,
          };
          // this is because laravel's pagination is different
          // when using api resources and standalone eloquent queries
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