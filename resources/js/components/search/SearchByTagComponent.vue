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

            <!-- Announcements 1 -->
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
                    @pagination-change-page="getSearchByTagAnnouncements"
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
            tag: {
                id: "",
                title: "",
                parent_id: "",
                is_public: ""
            },
            announcements: {}
        };
    },
    created: function() {
        this.getSearchByTagAnnouncements();
    },
    methods: {
        getSearchByTagAnnouncements(page = 1) {
            axios
                .get("/api/search/tag/" + this.id + "?page=" + page)
                .then(response => {
                    if (response.data.data.length > 0) {
                        this.announcements = response.data;
                        this.tag = response.data.data[0].tags.find(
                            e => e.id === this.id
                        );
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
