<template>
    <div class="columns mx-0 is-full">
        <div class="column">
            <div class="tags">
                <span
                    class="tag is-dark is-clickable"
                    v-for="tag in filteredTags"
                    v-bind:key="tag.id"
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
        }
    },
    computed: {
        filteredTags: function() {
            let vm = this;
            return vm.tags.filter(function(el) {
                return el.parent_id;
            });
        }
    }
};
</script>
