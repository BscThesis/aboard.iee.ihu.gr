<template>
    <div>
        <nav
            class="is-flex is-justify-content-space-between breadcrumb is-small has-none-separator"
            aria-label="breadcrumbs"
        >
            <ul class="is-align-items-center">
                <li class="mx-2 is-active">
                    <span class="icon"
                        ><i class="far fa-calendar-check fa-lg"></i
                    ></span>
                    <a>{{ updated_at }}</a>
                </li>
                <li class="mx-2">
                    <a>{{ author.name }}</a>
                </li>
                <li v-if="tags.length > 0" class="mx-2">
                    <!-- Tags -->
                    <tags-default-component
                        v-bind:tags="tags"
                    ></tags-default-component>
                </li>
            </ul>
            <ul
                v-if="attachments.length > 0"
                class="is-align-items-center is-justify-content-right"
            >
                <li class="mx-2">
                    <!-- Attachments -->
                    <span class="icon" :title="setTitle()"
                        ><i class="far fa-file-alt fa-lg"></i
                    ></span>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
export default {
    props: {
        tags: {
            type: Array,
            required: true
        },
        attachments: {
            type: Array,
            required: true
        },
        updated_at: {
            type: String,
            required: true
        },
        author: {
            type: Object,
            required: true
        }
    },
    methods: {
        setTitle() {
            let title = "";
            let counter = 0;
            this.attachments.forEach(attachment => {
                if (counter > 0) {
                    title += ", ";
                }
                title += attachment.filename;
                counter++;
            });
            return title;
        }
    }
};
</script>
