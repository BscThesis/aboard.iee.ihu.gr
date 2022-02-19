<template>
    <div class="block">
        <loader-component v-if="tagsOptions.length == 0"></loader-component>
        <div class="block" v-if="tagsOptions.length > 0">
            <div class="columns is-centered">
                <div class="column is-half">
                    <nav class="panel">
                        <p class="panel-heading">Ετικέτες</p>
			<!-- Tags -->
                        <treeselect
                            id="tags"			    
                            :multiple="true"                            
                            :options="tagsOptions"
                            :normalizer="tagNormalizer"
                            placeholder="Επιλέξτε ετικέτες..."
                            v-model="tags"
                            v-on:input="tagValueChange"
                        />                     
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
                                <a>{{ tag.title }}</a>
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
import Treeselect from "@riophae/vue-treeselect";

export default {
    components: { Treeselect },
    data: function() {
        return {
            tags: [],
	    tagsOptions: [],
            tagNormalizer(node) {
                return {
                    label: node.title,
                    children: node.childrensub_recursive
                };
            },
            btnLoading: false,
            subscriptions: []
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
                .get("/api/subscribetags")
                .then(function(response) {
                    let apiTags = response.data;
                    vm.tagsOptions = vm.removeEmptyChildrenTags(apiTags);
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
	removeEmptyChildrenTags(data) {	   
            for (let index = 0; index < data.length; index++) {		
                if (data[index].childrensub_recursive.length != 0) {
                    this.removeEmptyChildrenTags(
                        data[index].childrensub_recursive
                    );
                } else {
                    delete data[index]["childrensub_recursive"];
                }
            }
            return data;
        },
        getSelectedTags: function() {
            let vm = this;
            axios
                .get("/api/auth/subscriptions")
                .then(function(response) {
                    let tagArray = [];
                    vm.subscriptions = response.data;
                    if (vm.subscriptions.length > 0) {
                        vm.subscriptions.forEach(element => {
                            tagArray.push(element.id);
                        });
                    }
                    vm.tags = tagArray;
                })
                .catch(function(error) {
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                });
        },
        savePreferences: function() {
            let vm = this;
            if (vm.tags.length >= 0) {
                vm.btnLoading = true;
                axios
                    .post("/api/auth/subscribe", {
                        tags: JSON.stringify(vm.tags)
                    })
                    .then(function(response) {
                        vm.btnLoading = false;
                        vm.getSelectedTags();
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
        },
	tagValueChange: function() {
            this.savePreferences();
        }
    },
    computed: {
        selectedTagsNames: function() {
            let vm = this;
            let tagArray = [];
            if (vm.subscriptions.length > 0) {
                vm.subscriptions.forEach(element => {
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
