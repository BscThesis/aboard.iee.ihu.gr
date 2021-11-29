<template>
    <div class="column">
        <a v-bind:href="'/announcements/' + id">
            <h5 class="title is-5 is-marginless">{{ title }}</h5>
        </a>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        id: {
            type: Number,
            required: true
        }
    }
};
</script>
