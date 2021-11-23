<template>
    <div class="box p-2 mb-1">
        <article class="media">
            <div class="media-content is-clipped">
                <div class="content">
                    <div class="columns is-multiline is-mobile">
                        <span
                            v-if="
                                announcement.is_pinned == 1 &&
                                    announcement.pinned_until >= getNow()
                            "
                            class="icon mt-3 ml-3"
                        >
                            <i class="fas fa-thumbtack fa-lg fa-rotate-325"></i>
                        </span>
                        <!-- title -->
                        <single-announcement-title
                            v-if="displayEnglish == false"
                            v-bind:title="announcement.title"
                            v-bind:id="announcement.id"
                        ></single-announcement-title>

                        <single-announcement-title
                            v-if="displayEnglish == true"
                            v-bind:title="announcement.eng_title"
                            v-bind:id="announcement.id"
                        ></single-announcement-title>

                        <!-- English -->
                        <div
                            class="column is-narrow"
                            v-if="announcement.has_eng"
                        >
                            <span
                                class="icon is-clickable"
                                @click="displayEnglish = !displayEnglish"
                                title="This announcement is available in English"
                            >
                                <i class="fas fa-exchange-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <hr class="my-1" />

                <!-- Single Announcement Info -->
                <single-announcement-info-component
                    v-bind:tags="announcement.tags"
                    v-bind:attachments="announcement.attachments"
                    v-bind:updated_at="announcement.updated_at"
                    v-bind:author="announcement.author"
                ></single-announcement-info-component>
            </div>
        </article>
    </div>
</template>

<script>
export default {
    props: {
        announcement: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            displayEnglish: false,
            open: false
        };
    },
    methods: {
        getNow() {
            const today = new Date();
            const date =
                today.getFullYear() +
                "-" +
                (today.getMonth() + 1) +
                "-" +
                today.getDate();
            const time =
                today.getHours() +
                ":" +
                today.getMinutes() +
                ":" +
                today.getSeconds();
            return date + " " + time;
        }
    }
};
</script>
