<template>
    <div class="mt-5 box is-flex is-justify-content-center">
        <h1 class="title is-unselectable">
            {{ title }}
        </h1>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        }
    },
    data() {
        return {};
    }
};
</script>
