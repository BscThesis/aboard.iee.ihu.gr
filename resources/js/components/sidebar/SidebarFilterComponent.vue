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
                :options="tags"
                :normalizer="tagNormalizer"
                placeholder="Επιλέξτε ετικέτες..."
                v-model="selectedTagValues"
            />
        </section>

        <!-- Professors -->
        <section class="mb-4">
            <label for="profs">Καθηγητές</label>
            <treeselect
                id="profs"
                :multiple="true"
                :options="profs"
                :normalizer="profNormalizer"
                placeholder="Επιλέξτε καθηγητές..."
                v-model="selectedProfValues"
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
        <section>
            <label for="layout">Επιλογή εμφάνισης</label>
            <treeselect
                id="layout"
                :multiple="false"
                :options="layout"
                placeholder="Layout"
                :clearable="false"
                v-model="selectedLayoutValue"
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
        }
    },
    components: { Treeselect },
    data: () => ({
        selectedTagValues: [],
        tags: [],
        tagNormalizer(node) {
            return {
                label: node.title,
                children: node.children_recursive
            };
        },
        selectedProfValues: [],
        profs: [],
        profNormalizer(node) {
            return {
                label: node.name
            };
        },
        selectedSortValue: null,
        sort: [
            "Ετικέτες Α-Ζ",
            "Ετικέτες Ζ-Α",
            "Καθηγητές Α-Ζ",
            "Καθηγητές Ζ-Α",
            "Νεότερα",
            "Παλαιότερα"
        ].map((value, index) => ({
            id: index,
            label: value
        })),
        selectedLayoutValue: "List",
        layout: ["List", "Compact", "Box"].map((value, index) => ({
            id: index,
            label: value
        }))
    }),
    created: function() {
        this.getTags();
        this.getProfs();
    },
    methods: {
        getTags: function() {
            let vm = this;
            axios
                .get("/api/filtertags")
                .then(response => {
                    let apiTags = response.data;
                    vm.tags = vm.removeEmptyChildrenTags(apiTags);
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
                .get("/api/auth/authors")
                .then(response => {
                    vm.profs = response.data;
                })
                .catch(() => {
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
