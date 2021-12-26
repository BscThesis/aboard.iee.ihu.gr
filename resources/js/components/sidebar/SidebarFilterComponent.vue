<template>
    <aside
        v-bind:class="{ sidebarShow: !showFilters }"
        class="column is-one-quarter-tablet is-one-fifth-fullhd is-fullheight section"
    >
        <p class="menu-label">Φίλτρα</p>
        <!-- Search -->
        <search-default v-bind:searchProp.sync="search"></search-default>

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

export default {
    props: {
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
        searchProp: {
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
    components: { Treeselect },
    data: () => ({
        search: JSON.stringify(""),
        users: [],
        tags: [],
        perPage: localStorage.getItem("per_page")
            ? parseInt(localStorage.getItem("per_page"))
            : 10,
        sortId: 0,
        tagsOptions: [],
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
        perPageOptions: [5, 10, 15, 20].map(value => ({
            id: value,
            label: value
        }))
    }),
    watch: {
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
        search: {
            handler: function() {
                this.$emit("update:searchProp", this.search);
                this.getTags();
                this.getProfs();
            }
        },
        queryParamsProp: {
            handler: function() {
                if (this.queryParamsProp) {
                    this.setParams();
                }
            }
        }
    },
    mounted: function() {
        this.getTags();
        this.getProfs();
    },
    methods: {
        setParams() {
            this.search = this.searchProp;
            this.users = this.usersProp;
            this.tags = this.tagsProp;
            this.perPage = this.perPageProp;
            this.sortId = this.sortProp;
        },
        getTags: function() {
            let vm = this;
            let selected = {
                users: this.users,
                q: this.search
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
                q: this.search
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
        }
    }
};
</script>
