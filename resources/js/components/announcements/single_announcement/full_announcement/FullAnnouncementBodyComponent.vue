<template>
    <div class="column content has-text-justified is-marginless is-full">
        <div v-html="body"></div>
        <template v-if="attachments.length > 0">
            <attachments v-bind:attachments="attachments"></attachments>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        body: {
            type: String,
            required: true
        },
        attachments: {
            type: Array,
            required: true
        }
    }
};
</script>

<style scoped>
.content-mini {
    max-height: 6rem;
    overflow: hidden;
}
</style>
