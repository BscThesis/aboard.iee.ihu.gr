<template>
    <div class="my-4">
        <div class="columns is-mobile">
            <div class="column is-narrow">
                <span class="has-text-weight-bold">Συνημμένα:</span>
            </div>
            <div class="column">
                <div class="field is-grouped is-grouped-multiline">
                    <div
                        class="control"
                        v-for="attachment in attachments"
                        v-bind:key="attachment.id"
                    >
                        <div
                            class="tags"
                            v-bind:class="{
                                'has-addons': attachment.filename.endsWith(
                                    '.pdf'
                                )
                            }"
                        >
                            <a
                                class="tag is-info is-light"
                                v-bind:href="
                                    attachment.attachment_url
                                "
                                target="_blank"
                                title="Download"
                            >
                                {{ attachment.filename }}
                            </a>
                            <a
                                class="tag"
                                v-bind:href="
                                    attachment.attachment_url_view
                                "
                                target="_blank"
                                v-if="attachment.filename.endsWith('.pdf')"
                                title="View"
                            >
                                <span class="icon">
                                    <i class="far fa-eye"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        attachments: {
            type: Array,
            required: true
        }
    }
};
</script>
