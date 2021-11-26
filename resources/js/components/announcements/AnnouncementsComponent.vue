<template>
    <section
        class="main-content is-justify-content-center columns is-mobile is-fullheight"
    >
        <sidebar
            v-bind:usersProp.sync="selected.users"
            v-bind:tagsProp.sync="selected.tags"
            v-bind:perPageProp.sync="selected.perPage"
            v-bind:sortProp.sync="selected.sortId"
            v-bind:showFilters="showFilters"
            v-bind:searchProp.sync="selected.q"
        ></sidebar>
        <div
            v-bind:class="{ noDisplay: showFilters }"
            id="content"
            class="column is-9 block"
        >
            <!-- Title -->
            <page-title title="Ανακοινώσεις"></page-title>

            <!-- Links -->
            <front-page-links></front-page-links>

            <!-- Announcements -->
            <div class="block is-clipped">
                <!-- Loader -->
                <loader-component></loader-component>

                <!-- Announcement Default Layout -->
                <single-announcement-component-default
                    v-for="announcement in announcements.data"
                    v-bind:key="announcement.id"
                    v-bind:announcement="announcement"
                ></single-announcement-component-default>
                <!-- Announcement loop -->
                <!-- <single-announcement-component
                        v-for="announcement in announcements.data"
                        v-bind:key="announcement.id"
                        v-bind:announcement="announcement"
                    ></single-announcement-component> -->
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
        <button
            @click="filtersShow()"
            class="filters-fab-btn-icon button is-link is-rounded"
        >
            <i class="fas fa-sliders-h"></i>
        </button>
    </section>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
    data: function() {
        return {
            announcements: {},
            showFilters: false,
            selected: {
                users: [],
                tags: [],
                perPage: 10,
                sortId: 0,
                q: JSON.stringify("")
            }
        };
    },
    mounted: function() {
        this.getAnnouncements();
    },
    watch: {
        selected: {
            handler: function() {
                this.getAnnouncements();
            },
            deep: true
        }
    },
    methods: {
        filtersShow() {
            this.showFilters = !this.showFilters;
        },
        getAnnouncements(page = 1) {
            axios
                .get("/api/announcements?page=" + page, {
                    params: this.selected
                })
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
