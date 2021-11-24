<template>
    <aside
        v-bind:class="{ sidebarShow: !showFilters }"
        class="column is-one-quarter-tablet is-one-fifth-fullhd is-fullheight section"
    >
        <p class="menu-label">Φίλτρα</p>
        <!-- Search -->
        <search-default></search-default>

        <!-- Tags -->
        <section class="mb-4">
            <label for="tags">Ετικέτες</label>
            <treeselect
                id="tags"
                :multiple="true"
                :options="tagsOptions"
                :normalizer="tagNormalizer"
                placeholder="Επιλέξτε ετικέτες..."
                v-model="selected.tags"
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
                v-model="selected.users"
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
                placeholder="Tαξινόμηση"
                v-model="selectedSortValue"
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

        <section>
            <label for="perPage">Επιλογή ανά σελίδα:</label>
            <treeselect
                id="perPage"
                :multiple="false"
                :options="perPageOptions"
                placeholder="Per Page"
                :clearable="false"
                v-model="selected.perPage"
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
        }
    },
    components: { Treeselect },
    data: () => ({
        selected: {
            users: [],
            tags: [],
            perPage: 10
        },
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
        selectedSortValue: null,
        sort: ["Νεότερα", "Παλαιότερα"].map(value => ({
            id: value,
            label: value
        })),
        selectedLayoutValue: 0,
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
        selected: {
            handler: function() {
                this.getTags();
                this.getProfs();
            },
            deep: true
        }
    },
    mounted: function() {
        this.getTags();
        this.getProfs();
    },
    methods: {
        getTags: function() {
            let vm = this;
            axios
                .get("/api/filtertags", {
                    params: _.omit(this.selected, "tags")
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
            axios
                .get("/api/auth/authors", {
                    params: _.omit(this.selected, "users")
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
        layoutChange: function(node, instanceId) {
            // Κάνω την αλλαγή πρώτα στη βάση και μετά καλώ από τον parent για να δω τι layout διάλεξε ο χρήστης
            this.$parent.getAnnouncements(2);
        },
        profValueChange: function() {
            this.$emit("update:usersProp", this.selected.users);
        },
        tagValueChange: function() {
            this.$emit("update:tagsProp", this.selected.tags);
        },
        perPageChange: function() {
            this.$emit("update:perPageProp", this.selected.perPage);
        }
    }
};
</script>
