<template>
    <div class="block">
        <!-- Title -->
        <page-title title="Ανακοινώσεις"></page-title>

        <!-- Links -->
        <front-page-links></front-page-links>

        <!-- Announcements -->
        <div class="block is-clipped">
            <!-- Loader -->
            <loader-component></loader-component>

            <!-- Announcement loop -->
            <!-- <single-announcement-component
                v-for="announcement in announcements.data"
                v-bind:key="announcement.id"
                v-bind:announcement="announcement"
            ></single-announcement-component> -->

            <!-- Announcement Default Layout -->
            <single-announcement-component-default
                v-for="announcement in announcements.data"
                v-bind:key="announcement.id"
                v-bind:announcement="announcement"
            ></single-announcement-component-default>
        </div>

        <!-- Pagination -->
        <pagination
            :data="announcements"
            :limit="1"
            :show-disabled="true"
            @pagination-change-page="getAnnouncements"
        >
            <span slot="prev-nav">Previous</span>
            <span slot="next-nav">Next</span>
        </pagination>
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
        this.getAnnouncements();
    },
    methods: {
        getAnnouncements(page = 1) {
            axios
                .get("/api/announcements?page=" + page)
                .then(response => {
                    this.announcements = response.data;
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
