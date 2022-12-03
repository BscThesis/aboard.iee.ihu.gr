<template>
    <div class="columns mx-0 is-full">
        <div class="column">
            <div class="tags">
                <span
                    class="tag is-dark is-clickable"
                    v-for="tag in filteredTags"
                    v-bind:key="tag.id"
                    @click="updateSearchURL(tag.id)"
                    >{{ tag.title }}</span
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        tags: {
            type: Array,
            required: true
        },
        tagFilterProp: {
            type: Array,
            required: false
        },
        queryParamsFilterProp: {
            type: Boolean,
            required: false
        }
    },
    computed: {
        filteredTags: function() {
            let vm = this;
            return vm.tags.filter(function(el) {
                return el.parent_id;
            });
        }
    },
    methods: {
        updateSearchURL(id) {
            if (this.tagFilterProp) {
                this.$emit("update:tagsProp", this.updateTags(id));
                if (!this.queryParamsFilterProp) {
                    this.$emit("update:queryParamsFilterProp", true);
                }
            }
        },
        updateTags: function(id) {
            if (!this.tagFilterProp.includes(id)) this.tagFilterProp.push(id);
        }
    }
};
</script>
