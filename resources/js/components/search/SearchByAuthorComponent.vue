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
                    @pagination-change-page="getSearchByAuthorAnnouncements"
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
    props: {
        id: {
            type: Number,
            required: true
        }
    },
    data: function() {
        return {
            author: {
                id: "",
                name: ""
            },
            announcements: {}
        };
    },
    created: function() {
        this.getSearchByAuthorAnnouncements();
    },
    methods: {
        getSearchByAuthorAnnouncements(page = 1) {
            axios
                .get("/api/search/author/" + this.id + "?page=" + page)
                .then(response => {
                    if (response.data.data.length > 0) {
                        this.announcements = response.data;
                        this.author = response.data.data[0].author;
                    } else {
                        toast({
                            message: "Δε βρέθηκε",
                            type: "is-danger",
                            position: "bottom-right"
                        });
                    }

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
