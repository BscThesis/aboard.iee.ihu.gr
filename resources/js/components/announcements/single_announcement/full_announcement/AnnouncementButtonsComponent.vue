<template>
    <div class="block">
        <div v-if="announcement.id" class="buttons is-centered">
            <back-button-component></back-button-component>
            <edit-button-component
                v-if="is_admin || is_the_author"
                v-bind:id="announcement.id"
            ></edit-button-component>
            <delete-button-component
                v-if="is_admin || is_the_author"
                v-bind:announcement="announcement"
            ></delete-button-component>
        </div>
    </div>
</template>

<script>
import userMixin from "../mixins/userMixin";

export default {
    mixins: [userMixin],
    props: {
        announcement: {
            type: Object,
            required: true
        }
    },
    data: function() {
        return {
            is_admin: false,
            is_the_author: false
        };
    },
    mounted: function() {
        if (user_info) {
            this.is_admin = JSON.parse(user_info).is_admin;
            this.is_the_author =
                JSON.parse(user_info).id == this.announcement.author.id;
        }
    }
};
</script>
