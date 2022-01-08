<template>
    <renderless-laravel-vue-pagination
        :data="data"
        :limit="limit"
        :show-disabled="showDisabled"
        :size="size"
        v-on:pagination-change-page="onPaginationChangePage"
    >
        <ul
            class="pagination pagination-list is-justify-content-end"
            :class="{
                'is-small': size == 'small',
                'is-medium': size == 'medium',
                'is-large': size == 'large'
            }"
            v-if="computed.total > computed.perPage"
            slot-scope="{
                showDisabled,
                size,
                computed,
                prevButtonEvents,
                nextButtonEvents,
                pageButtonEvents
            }"
        >
            <li v-if="computed.prevPageUrl || showDisabled">
                <a
                    :disabled="!computed.prevPageUrl"
                    class="pagination-previous"
                    href="#"
                    aria-label="Previous"
                    :tabindex="!computed.prevPageUrl && -1"
                    v-on="prevButtonEvents"
                >
                    <slot name="prev-nav">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </slot>
                </a>
            </li>

            <li v-for="(page, key) in computed.pageRange" :key="key">
                <a
                    class="pagination-link"
                    :class="{ 'is-current': page == computed.currentPage }"
                    href="#"
                    v-on="pageButtonEvents(page)"
                >
                    {{ page }}
                    <span class="sr-only" v-if="page == computed.currentPage"
                        >(current)</span
                    >
                </a>
            </li>

            <li v-if="computed.nextPageUrl || showDisabled">
                <a
                    :disabled="!computed.nextPageUrl"
                    class="pagination-next"
                    href="#"
                    aria-label="Next"
                    :tabindex="!computed.nextPageUrl && -1"
                    v-on="nextButtonEvents"
                >
                    <slot name="next-nav">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </slot>
                </a>
            </li>
        </ul>
    </renderless-laravel-vue-pagination>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            default: () => {}
        },
        limit: {
            type: Number,
            default: 0
        },
        showDisabled: {
            type: Boolean,
            default: false
        },
        size: {
            type: String,
            default: "default",
            validator: value => {
                return (
                    ["small", "default", "medium", "large"].indexOf(value) !==
                    -1
                );
            }
        }
    },

    methods: {
        onPaginationChangePage(page) {
            this.$emit("pagination-change-page", page);
        }
    }
};
</script>
