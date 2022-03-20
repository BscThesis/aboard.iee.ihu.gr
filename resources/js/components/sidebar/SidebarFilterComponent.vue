<template>
    <aside
        v-bind:class="{ sidebarShow: !showFilters }"
        class="column is-one-quarter-tablet is-one-fifth-fullhd is-fullheight section"
    >
        <p class="menu-label">Φιλτρα</p>

        <label for="searchTitle">Αναζήτηση Τίτλου</label>

        <!-- Title Search -->
        <search-default
            id="searchTitle"
            title="Αναζήτηση..."
            v-bind:searchProp.sync="searchTitle"
        ></search-default>

        <label for="searchBody">Αναζήτηση Κειμένου</label>

        <!-- Body Search -->
        <search-default
            id="searchBody"
            title="Αναζήτηση..."
            v-bind:searchProp.sync="searchBody"
        ></search-default>

        <!-- Tags -->
        <section class="mb-4">
            <label for="tags">Ετικέτες</label>
            <treeselect
                id="tags"
                :multiple="true"
                :options="tagsOptions"
                :normalizer="tagNormalizer"
                placeholder="Επιλέξτε ετικέτες..."
                v-model="tags"
                v-on:input="tagValueChange"
            />
        </section>

        <!-- Professors -->
        <section class="mb-4">
            <label for="profs">Καθηγητές</label>
            <treeselect
                id="profs"
                :multiple="true"
                :options="profsOptions"
                :normalizer="profNormalizer"
                placeholder="Επιλέξτε καθηγητές..."
                v-model="users"
                v-on:input="profValueChange"
            />
        </section>

        <!-- Announcements created at -->
        <section class="mb-4">
            <label for="updatedAt">Επιλογή διαστήματος ανακοινώσεων:</label>
            <treeselect
                id="updatedAt"
                :multiple="false"
                :options="updatedAtOptions"
                placeholder="Updated At:"
                :clearable="false"
                v-model="updatedAt"
                v-on:input="updatedAtChange"
            />
        </section>

        <template v-if="updatedAt === 5">
            <section class="mb-4">
                <label for="updatedAfter">
                    Εμφάνιση ανακοινώσεων από
                </label>
                <flat-pickr
                    id="updatedAfter"
                    v-model="updatedAfter"
                    :config="config"
                ></flat-pickr>
            </section>

            <section class="mb-4">
                <label for="updatedBefore">
                    Εμφάνιση ανακοινώσεων μέχρι
                </label>
                <flat-pickr
                    id="updatedBefore"
                    v-model="updatedBefore"
                    :config="config"
                ></flat-pickr>
            </section>
        </template>

        <!-- Sort -->
        <section class="mb-4">
            <label for="sort">Επιλογή ταξινόμησης</label>
            <treeselect
                id="sort"
                :multiple="false"
                :options="sort"
                :clearable="false"
                placeholder="Tαξινόμηση"
                v-model="sortId"
                v-on:input="sortIdChange"
            />
        </section>

        <!-- Layout Selection -->
        <section class="mb-4">
            <label for="layout">Επιλογή εμφάνισης</label>
            <treeselect
                id="layout"
                :multiple="false"
                :options="layout"
                placeholder="Layout"
                :clearable="false"
                v-model="selectedLayoutValue"
                v-on:input="layoutChange"
            />
        </section>

        <!-- Announcements Per Page Selection -->
        <section>
            <label for="perPage">Επιλογή ανά σελίδα:</label>
            <treeselect
                id="perPage"
                :multiple="false"
                :options="perPageOptions"
                placeholder="Per Page"
                :clearable="false"
                v-model="perPage"
                v-on:input="perPageChange"
            />
        </section>
    </aside>
</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import { toast } from "bulma-toast";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
    props: {
        updatedAfterProp: {
            type: String,
            required: true
        },
        updatedBeforeProp: {
            type: String,
            required: true
        },
        showFilters: {
            type: Boolean,
            required: true
        },
        usersProp: {
            type: Array,
            required: true
        },
        tagsProp: {
            type: Array,
            required: true
        },
        perPageProp: {
            type: Number,
            required: true
        },
        sortProp: {
            type: Number,
            required: true
        },
        searchTitleProp: {
            type: String,
            required: true
        },
        searchBodyProp: {
            type: String,
            required: true
        },
        layoutProp: {
            type: Number,
            required: true
        },
        queryParamsProp: {
            type: Boolean,
            required: true
        }
    },
    components: { Treeselect, flatPickr },
    data: () => ({
        updatedBefore: "",
        updatedAfter: "",
        config: {
            altInput: true,
            enableTime: true,
            defaultHour: 0,
            minuteIncrement: 1,
            time_24hr: true,
            // minDate: new Date().fp_incr(1),
            dateFormat: "Y-m-d H:i"
        },
        searchTitle: "",
        searchBody: "",
        users: [],
        tags: [],
        // Add treeselect options and default values
        tagsOptions: [],
        // Change treeselect option names
        tagNormalizer(node) {
            return {
                label: "[" + node.announcements_count + "] " + node.title,
                children: node.children_recursive
            };
        },
        profsOptions: [],
        profNormalizer(node) {
            return {
                label: "[" + node.announcements_count + "] " + node.name
            };
        },
        sortId: 0,
        sort: ["Pinned First", "Newest", "Oldest"].map((value, index) => ({
            id: index,
            label: value
        })),
        selectedLayoutValue: localStorage.getItem("layout")
            ? parseInt(localStorage.getItem("layout"))
            : 0,
        layout: ["List", "Compact", "Box"].map((value, index) => ({
            id: index,
            label: value
        })),
        perPage: localStorage.getItem("per_page")
            ? parseInt(localStorage.getItem("per_page"))
            : 10,
        perPageOptions: [5, 10, 15, 20].map(value => ({
            id: value,
            label: value
        })),
        updatedAt: 4,
        updatedAtOptions: [
            "Last hour",
            "Last day",
            "Last week",
            "Last month",
            "Last 6 months",
            "Custom"
        ].map((value, index) => ({
            id: index,
            label: value
        }))
    }),
    watch: {
        updatedAfter: {
            handler: function() {
                if (this.updatedAt === 5) {
                    this.$emit(
                        "update:updatedAfterProp",
                        JSON.stringify(this.updatedAfter)
                    );
                    this.getTags();
                    this.getProfs();
                }
            }
        },
        updatedBefore: {
            handler: function() {
                this.$emit(
                    "update:updatedBeforeProp",
                    JSON.stringify(this.updatedBefore)
                );

                if (this.updatedAt === 5) {
                    this.getTags();
                    this.getProfs();
                }
            }
        },
        updatedAt: {
            handler: function() {
                if (this.updatedAt !== 5) {
                    this.updatedBefore = "";
                    this.updatedAfter = "";
                    this.getTags();
                    this.getProfs();
                }
            }
        },
        users: {
            handler: function() {
                this.getTags();
            }
        },
        tags: {
            handler: function() {
                this.getProfs();
            }
        },
        // Update searchTitleProp and get tags and professors again
        searchTitle: {
            handler: function() {
                this.$emit(
                    "update:searchTitleProp",
                    JSON.stringify(this.searchTitle)
                );
                this.getTags();
                this.getProfs();
            }
        },
        // Update searchBodyProp and get tags and professors again
        searchBody: {
            handler: function() {
                this.$emit(
                    "update:searchBodyProp",
                    JSON.stringify(this.searchBody)
                );
                this.getTags();
                this.getProfs();
            }
        },
        // If queryParamsProp changes and is set to true means that the url is sent by someone and initializes params
        queryParamsProp: {
            handler: function() {
                if (this.queryParamsProp) {
                    this.setParams();
                }
            }
        }
    },
    // When mounted is triggered during Vue Instance LifeCycle, get tags and professors
    mounted: function() {
        this.getTags();
        this.getProfs();
    },
    computed: {
        updated: function() {
            switch (this.updatedAt) {
                case 0:
                    return "last_hour";
                case 1:
                    return "last_day";
                case 2:
                    return "last_week";
                case 3:
                    return "last_month";
                case 4:
                    return "last_6months";
                default:
                    break;
            }
        }
    },
    methods: {
        setParams() {
            this.searchTitle = this.searchTitleProp;
            this.searchBody = this.searchBodyProp;
            this.users = this.usersProp;
            this.tags = this.tagsProp;
            this.perPage = this.perPageProp;
            this.sortId = this.sortProp;
        },
        getTags: function() {
            let vm = this;
            let selected = {
                users: this.users,
                title: JSON.stringify(this.searchTitle),
                body: JSON.stringify(this.searchBody),
                updatedAfter: JSON.stringify(
                    this.updatedAt !== 5 ? this.updated : this.updatedAfter
                ),
                updatedBefore: JSON.stringify(this.updatedBefore)
            };
            axios
                .get("/api/filtertags", {
                    params: selected
                })
                .then(response => {
                    let apiTags = response.data;
                    vm.tagsOptions = vm.removeEmptyChildrenTags(apiTags);
                })
                .catch(() => {
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                });
        },
        removeEmptyChildrenTags(data) {
            for (let index = 0; index < data.length; index++) {
                if (data[index].children_recursive.length != 0) {
                    this.removeEmptyChildrenTags(
                        data[index].children_recursive
                    );
                } else {
                    delete data[index]["children_recursive"];
                }
            }
            return data;
        },
        getProfs: function() {
            let vm = this;
            let selected = {
                tags: this.tags,
                title: JSON.stringify(this.searchTitle),
                body: JSON.stringify(this.searchBody),
                updatedAfter: JSON.stringify(
                    this.updatedAt !== 5 ? this.updated : this.updatedAfter
                ),
                updatedBefore: JSON.stringify(this.updatedBefore)
            };
            axios
                .get("/api/auth/authors", {
                    params: selected
                })
                .then(response => {
                    vm.profsOptions = response.data;
                })
                .catch(() => {
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                });
        },
        layoutChange: function() {
            localStorage.setItem("layout", this.selectedLayoutValue);
            this.$emit("update:layoutProp", this.selectedLayoutValue);
        },
        profValueChange: function() {
            this.$emit("update:usersProp", this.users);
        },
        tagValueChange: function() {
            this.$emit("update:tagsProp", this.tags);
        },
        perPageChange: function() {
            localStorage.setItem("per_page", this.perPage);
            this.$emit("update:perPageProp", this.perPage);
        },
        sortIdChange: function() {
            this.$emit("update:sortProp", this.sortId);
        },
        updatedAtChange: function() {
            if (this.updatedAt !== 5)
                this.$emit(
                    "update:updatedAfterProp",
                    JSON.stringify(this.updated)
                );
        }
    }
};
</script>
