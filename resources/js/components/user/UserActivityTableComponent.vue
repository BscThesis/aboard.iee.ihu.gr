<template>
  <div class="block">
    <table class="table is-fullwidth is-hoverable">
      <thead>
        <tr>
          <th></th>
          <th class="is-uppercase">Type</th>
          <th class="is-uppercase">Link</th>
          <th class="is-uppercase">User</th>
          <th class="is-uppercase">Date</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="notification in notifications" v-bind:key="notification.id">
          <th v-bind:title="notification.id">
            <span class="icon has-text-info">
              <i class="fas fa-info"></i>
            </span>
          </th>
          <td>{{ notification.data.type }}</td>
          <td>
            <a
              :href="'/announcements/' + notification.data.id"
              target="_blank"
              rel="noopener noreferrer"
              >{{ notification.data.title }}</a
            >
          </td>
          <td>{{ notification.data.user }}</td>
          <td>{{ notification.created_at }}</td>
        </tr>
      </tbody>
    </table>

    <div class="columns is-centered">
      <div class="column is-narrow">
        <pagination-component small></pagination-component>
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
      notifications: {},
    };
  },
  created: function () {
    this.fetchNotifications();
    bus.$on("next", (data) => {
      this.fetchNotifications(data);
    });
    bus.$on("prev", (data) => {
      this.fetchNotifications(data);
    });
  },
  methods: {
    fetchNotifications(page_url = "/api/auth/user/notifications") {
      let vm = this;
      page_url = page_url;
      axios
        .get(page_url)
        .then((response) => {
          vm.notifications = response.data.data;

          let links = {
            next: response.data.links.next,
            prev: response.data.links.prev,
          };

          let meta = {
            current_page: response.data.meta.current_page,
            last_page: response.data.meta.last_page,
          };

          let paginate = {
            links: links,
            meta: meta,
          };

          bus.$emit("paginationObject", paginate);
          bus.$emit("loadingFinished", true);
        })
        .catch((error) => {
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