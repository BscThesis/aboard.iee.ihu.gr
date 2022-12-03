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
                <tr
                    v-for="notification in notifications.data"
                    v-bind:key="notification.id"
                >
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
                <!-- Pagination -->
                <pagination
                    :data="notifications"
                    :limit="1"
                    :show-disabled="true"
                    :size="'small'"
                    @pagination-change-page="fetchNotifications"
                >
                    <span slot="prev-nav">Previous</span>
                    <span slot="next-nav">Next</span>
                </pagination>
            </div>
        </div>
    </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
    data: function() {
        return {
            notifications: {}
        };
    },
    created: function() {
        this.fetchNotifications();
    },
    methods: {
        fetchNotifications(page = 1) {
            axios
                .get("/api/auth/user/notifications?page=" + page)
                .then(response => {
                    this.notifications = response.data;
                    bus.$emit("loadingFinished", true);
                })
                .catch(() => {
                    bus.$emit("loadingFinished", true);
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                });
        }
    }
};
</script>
