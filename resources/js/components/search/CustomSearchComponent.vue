<template>
    <div class="block">
        <!-- Title -->
        <page-title title="Αποτελέσματα αναζήτησης"></page-title>

        <!-- Search/Pinned Box -->
        <search-component></search-component>

        <!-- Announcements -->
        <div class="block is-clipped">
            <!-- Loader -->
            <loader-component></loader-component>

            <!-- Announcement loop -->
            <single-announcement-component
                v-for="announcement in announcements.data"
                v-bind:key="announcement.id"
                v-bind:announcement="announcement"
            ></single-announcement-component>

            <!-- Pagination -->
            <pagination
                :data="announcements"
                :limit="1"
                :show-disabled="true"
                @pagination-change-page="getSearchedAnnouncements"
            >
                <span slot="prev-nav">Previous</span>
                <span slot="next-nav">Next</span>
            </pagination>
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
            required: true
        }
    },
    data: function() {
        return {
            announcements: {}
        };
    },
    created: function() {
        this.getSearchedAnnouncements();
    },
    methods: {
        getSearchedAnnouncements(page = 1) {
            axios
                .get("/api/search?page=" + page, {
                    params: {
                        q: JSON.stringify(this.params)
                    }
                })
                .then(response => {
                    this.announcements = response.data;
                    bus.$emit("loadingFinished", true);
                })
                .catch(function() {
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

<style></style>
