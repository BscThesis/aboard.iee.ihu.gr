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
            v-bind:layoutProp.sync="layout"
            v-bind:queryParamsProp.sync="queryParams"
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
                    v-bind:layout="layout"
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
            queryParams: false,
            // page: 1,
            selected: {
                users: [],
                tags: [],
                perPage: localStorage.getItem("per_page")
                    ? parseInt(localStorage.getItem("per_page"))
                    : 10,
                sortId: 0,
                q: ""
            },
            layout: localStorage.getItem("layout")
                ? parseInt(localStorage.getItem("layout"))
                : 0
        };
    },
    mounted: function() {
        this.getAnnouncements();
        this.setParamsToValues();
    },
    watch: {
        selected: {
            handler: function() {
                // if (this.queryParams) this.getAnnouncements(this.page);
                // else
                this.getAnnouncements();
                this.updateUrlParam();
            },
            deep: true
        }
        // page: {
        //     handler: function() {
        //         this.updateUrlParam();
        //     }
        // }
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
                    // this.page = page;
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
        },
        setParamsToValues() {
            var href = window.location.search;
            if (href.length > 0) {
                this.queryParams = true;
                let searchParams = new URLSearchParams(href);
                // this.page = parseInt(searchParams.get("page"));
                this.selected.perPage = parseInt(searchParams.get("perPage"));
                this.selected.sortId = parseInt(searchParams.get("sortId"));
                this.selected.q = JSON.parse(searchParams.get("q"));
                if (searchParams.get("tags").length > 0) {
                    const tagArray = this.convertStringArrayToIntegerArray(
                        searchParams.get("tags").split(",")
                    );
                    this.selected.tags = tagArray;
                }
                if (searchParams.get("users").length > 0) {
                    const userArray = this.convertStringArrayToIntegerArray(
                        searchParams.get("users").split(",")
                    );
                    this.selected.users = userArray;
                }
            }
        },
        convertStringArrayToIntegerArray(array) {
            array = array.map(x => +x);
            return array;
        },
        updateUrlParam() {
            let state = { ...this.selected };
            window.history.pushState(
                state,
                "",
                "?" + new URLSearchParams(state).toString()
            );
        }
    }
};
</script>
