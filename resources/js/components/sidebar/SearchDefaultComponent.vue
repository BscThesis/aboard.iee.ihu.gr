<template>
    <section class="mb-4">
        <p class="control has-icons-left has-icons-right">
            <input
                class="input is-rounded"
                type="text"
                :placeholder="title"
                v-model="search"
                v-on:keyup.enter="searchSet()"
            />
            <span class="icon is-left">
                <i class="fas fa-search" aria-hidden="true"></i>
            </span>
            <span v-show="search" class="icon is-small is-right">
                <a class="delete" @click="deleteSearch()"></a>
            </span>
        </p>
    </section>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        searchProp: {
            type: String,
            required: true
        }
    },
    data: function() {
        return {
            search: ""
        };
    },
    watch: {
        searchProp: {
            handler: function() {
                this.search = this.searchProp;
            }
        }
    },
    methods: {
        searchSet: function() {
            this.$emit("update:searchProp", this.search);
        },
        deleteSearch: function() {
            this.search = "";
            this.searchSet();
        }
    }
};
</script>

<style></style>
