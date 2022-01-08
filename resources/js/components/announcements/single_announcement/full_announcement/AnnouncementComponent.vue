<template>
    <div class="block">
        <div class="block" v-if="announcement.id">
            <!-- Announcement -->
            <section class="section">
                <div class="container">
                    <div class="columns is-mobile">
                        <div class="column">
                            <full-announcement-title
                                v-bind:title="
                                    displayEnglish
                                        ? announcement.eng_title
                                        : announcement.title
                                "
                            ></full-announcement-title>
                        </div>

                        <div
                            class="column is-narrow"
                            v-if="announcement.has_eng"
                        >
                            <span
                                class="icon is-clickable"
                                title="This announcement is available in English"
                                @click="displayEnglish = !displayEnglish"
                            >
                                <i class="fas fa-exchange-alt"></i>
                            </span>
                        </div>
                    </div>

                    <div class="is-divider"></div>

                    <full-announcement-body
                        v-bind:body="
                            displayEnglish
                                ? announcement.eng_body
                                : announcement.body
                        "
                        v-bind:attachments="announcement.attachments"
                    ></full-announcement-body>

                    <tags-default-component
                        v-if="announcement.tags"
                        v-bind:tags="announcement.tags"
                    ></tags-default-component>

                    <last-update
                        v-if="announcement.updated_at"
                        v-bind:updated_at="announcement.updated_at"
                        v-bind:author="announcement.author"
                    ></last-update>
                </div>
            </section>

            <!-- Buttons -->
            <announcement-buttons-component
                v-if="announcement"
                v-bind:announcement="announcement"
		:user="user"
            ></announcement-buttons-component>
        </div>
        <loader-component v-else></loader-component>
    </div>
</template>

<script>
import { toast } from "bulma-toast";
import { bus } from "../../../../app";

export default {
    props: {
        id: {
            type: Number,
            required: true
        },
	user: {
	    type: Object,
	    required: false
	}
    },
    data: function() {
        return {
            announcement: {},
            displayEnglish: false
        };
    },
    created: function() {
        this.getSingle();
        bus.$on("objectRemoved", data => {
            if (data) {
                window.location.replace("/announcements");
            }
        });
    },
    methods: {
        getSingle: function() {
            let vm = this;
            axios
                .get("/api/announcements/" + vm.id)
                .then(function(response) {
                    vm.announcement = response.data.data;
                })
                .catch(function(error) {
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                    console.log(error);
                });
        }
    }
};
</script>
