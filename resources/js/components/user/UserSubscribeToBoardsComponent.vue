<template>
    <div class="block">
        <loader-component v-if="tags.length == 0"></loader-component>
        <div class="block" v-if="tags.length > 0">
            <div class="columns is-centered">
                <div class="column is-half">
                    <nav class="panel">
                        <p class="panel-heading">Ετικέτες</p>
                        <div class="panel-block">
                            <p class="control has-icons-left has-icons-right">
                                <input
                                    class="input"
                                    type="text"
                                    placeholder="Αναζήτηση..."
                                    v-model="search"
                                />
                                <span class="icon is-left">
                                    <i
                                        class="fas fa-search"
                                        aria-hidden="true"
                                    ></i>
                                </span>
                                <span
                                    v-show="search"
                                    class="icon is-small is-right"
                                >
                                    <a class="delete" @click="search = ''"></a>
                                </span>
                            </p>
                        </div>
                        <div class="tags-list">
                            <label
                                v-for="tag in filteredTags"
                                v-bind:key="tag.id"
                                class="panel-block"
                            >
                                <input
                                    type="checkbox"
                                    v-bind:value="tag.id"
                                    v-model="selectedTags"
                                />
                                {{ tag.title }}
                            </label>
                        </div>
                        <div class="panel-block">
                            <button
                                class="button is-link is-outline is-fullwidth"
                                @click="savePreferences()"
                                v-bind:class="{ 'is-loading': btnLoading }"
                                v-bind:disabled="btnLoading"
                            >
                                Αποθήκευση
                            </button>
                        </div>
                    </nav>
                </div>
                <div class="column is-half">
                    <aside class="menu">
                        <p class="menu-label is-unselectable">
                            Subscribed boards
                        </p>
                        <ul class="menu-list">
                            <li
                                v-for="tag in selectedTagsNames"
                                v-bind:key="tag.id"
                            >
                                <a
                                    v-bind:href="'/search/tag/' + tag.id"
                                    target="_blank"
                                    >{{ tag.title }}</a
                                >
                            </li>
                        </ul>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { toast } from "bulma-toast";
import userMixin from "../mixins/userMixin";

export default {
    mixins: [userMixin],
    data: function() {
        return {
            tags: [],
            selectedTags: [],
            search: "",
            btnLoading: false
        };
    },
    mounted: function() {
        this.getAllTags();
        this.getSelectedTags();
    },
    methods: {
        getAllTags: function() {
            let vm = this;
            axios
                .get("/api/tags")
                .then(function(response) {
                    vm.tags = response.data.data;
                })
                .catch(function(error) {
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                    console.log(error);
                });
        },
        getSelectedTags: function() {
            let subscriptions = JSON.parse(user_info).subscriptions;
            let tagArray = [];
            let vm = this;

            if (subscriptions.length > 0) {
                subscriptions.forEach(element => {
                    tagArray.push(element.id);
                });
            }
            vm.selectedTags = tagArray;
        },
        savePreferences: function() {
            let vm = this;
            if (vm.selectedTags.length >= 0) {
                vm.btnLoading = true;
                axios
                    .post("/api/auth/subscribe", {
                        tags: JSON.stringify(vm.selectedTags)
                    })
                    .then(function(response) {
                        vm.btnLoading = false;
                        toast({
                            message: "Αποθηκεύτηκε",
                            type: "is-success",
                            position: "bottom-right"
                        });
                    })
                    .catch(function(error) {
                        vm.btnLoading = false;
                        toast({
                            message: "Συνέβη κάποιο σφάλμα",
                            type: "is-danger",
                            position: "bottom-right"
                        });
                        console.log(error);
                    });
            } else {
                toast({
                    message: "Επιλέξτε τουλάχιστον μία ετικέτα",
                    type: "is-danger",
                    position: "bottom-right"
                });
            }
        }
    },
    computed: {
        allTags: function() {
            let vm = this;
        },
        filteredTags: function() {
            let vm = this;
            if (vm.search == "") {
                return vm.tags.filter(function(el) {
                    return el.parent_id != null;
                });
                // return vm.tags;
            } else {
                return vm.tags.filter(function(el) {
                    return (
                        el.title
                            .toLowerCase()
                            .includes(vm.search.toLowerCase()) &&
                        el.parent_id != null
                    );
                });
            }
        },
        selectedTagsNames: function() {
            let vm = this;
            let subscriptions = JSON.parse(user_info).subscriptions;
            let tagArray = [];
            if (subscriptions.length > 0) {
                subscriptions.forEach(element => {
                    tagArray.push({ title: element.title, id: element.id });
                });
                return tagArray;
            }
        }
    }
};
</script>

<style scoped>
.tags-list {
    max-height: 20rem;
    overflow: auto;
}
.panel-block {
    padding: 12px 10px;
}
</style>
