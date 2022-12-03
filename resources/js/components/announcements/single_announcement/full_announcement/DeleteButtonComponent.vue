<template>
    <span v-if="announcement.id">
        <a
            class="button is-danger is-light"
            @click="openDeleteModal(announcement)"
        >
            <span class="icon">
                <i class="fas fa-trash"></i>
            </span>
            <span class="is-capitalized">Διαγραφή</span>
        </a>

        <!-- Delete post modal -->
        <delete-modal-component></delete-modal-component>
    </span>
</template>

<script>
import { bus } from "../../../../app";

export default {
    props: {
        announcement: {
            type: Object,
            required: true
        }
    },
    data: function() {
        return {};
    },
    methods: {
        // Open modal to ask if you are sure deleting the announcement
        openDeleteModal(announcement) {
            let vm = this;
            let forDeletion = {
                type: "announcement",
                data: announcement
            };
            bus.$emit("openModal", forDeletion);
        }
    }
};
</script>

<style></style>
