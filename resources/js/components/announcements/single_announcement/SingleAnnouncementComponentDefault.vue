<template>
    <div v-bind:class="layout == 0 ? 'p-2 mb-1' : ''" class="box">
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
                            v-bind:title="
                                displayEnglish
                                    ? announcement.eng_title
                                    : announcement.title
                            "
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
                        <template v-if="layout == 1">
                            <!-- Dropdown -->
                            <div class="column is-narrow">
                                <span class="is-pulled-right">
                                    <span
                                        class="icon is-clickable"
                                        @click="open = !open"
                                    >
                                        <i
                                            v-bind:class="
                                                open
                                                    ? 'fas fa-chevron-down'
                                                    : 'fas fa-chevron-right'
                                            "
                                        ></i>
                                    </span>
                                </span>
                            </div>

                            <!-- Announcement body -->
                            <full-announcement-body
                                v-bind:class="{ 'has-text-hidden': !open }"
                                v-bind:body="
                                    displayEnglish
                                        ? announcement.eng_body
                                        : announcement.body
                                "
                                v-bind:attachments="announcement.attachments"
                            ></full-announcement-body>
                        </template>
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
        },
        layout: {
            type: Number,
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
