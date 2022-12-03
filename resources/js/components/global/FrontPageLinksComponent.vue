<template>
    <div class="block" v-if="user.is_admin == true || user.is_author == true">
        <div class="buttons is-right">
            <button
                v-if="user.is_admin"
                class="button is-danger is-uppercase"
                @click="modalOpen = true"
            >
                Admin area
            </button>
            <button
                v-if="user.is_admin"
                class="button is-link is-uppercase"
                @click="viewIssues = true"
            >
                View issues
            </button>
            <a
                v-if="user.is_author"
                class="button is-dark is-capitalized"
                href="/announcements/create"
                >Προσθήκη ανακοίνωσης</a
            >
        </div>

        <!-- Admin area -->
        <div
            v-if="user.is_admin"
            class="modal"
            v-bind:class="{ 'is-active': modalOpen }"
        >
            <div class="modal-background" @click="modalOpen = false">></div>
            <div class="modal-card" style="width: 80%">
                <header class="modal-card-head">
                    <p class="modal-card-title">Διαχείριση ετικετών</p>
                    <button
                        class="delete"
                        aria-label="close"
                        @click="modalOpen = false"
                    ></button>
                </header>
                <section class="modal-card-body">
                    <admin-component></admin-component>
                </section>
                <footer class="modal-card-foot">
                    <button class="button" @click="modalOpen = false">
                        Exit
                    </button>
                </footer>
            </div>
        </div>

        <!-- View issues -->
        <div
            v-if="user.is_admin"
            class="modal"
            v-bind:class="{ 'is-active': viewIssues }"
        >
            <div class="modal-background" @click="viewIssues = false">></div>
            <div class="modal-card" style="width: 80%">
                <header class="modal-card-head">
                    <p class="modal-card-title">Προβολή προβλημάτων</p>
                    <button
                        class="delete"
                        aria-label="close"
                        @click="viewIssues = false"
                    ></button>
                </header>
                <section class="modal-card-body">
                    <issues-component></issues-component>
                </section>
                <footer class="modal-card-foot">
                    <button class="button" @click="viewIssues = false">
                        Exit
                    </button>
                </footer>
            </div>
        </div>
    </div>
</template>

<script>

export default {

    props: {
	user: Object,
	required: false
    },
    data: function() {
        return {
            modalOpen: false,
            viewIssues: false
        };
    },
    watch: {
        modalOpen: function(value) {
            if (value) {
                document
                    .getElementsByTagName("html")[0]
                    .classList.add("is-clipped");
            } else {
                document
                    .getElementsByTagName("html")[0]
                    .classList.remove("is-clipped");
            }
        },
        viewIssues: function(value) {
            if (value) {
                document
                    .getElementsByTagName("html")[0]
                    .classList.add("is-clipped");
            } else {
                document
                    .getElementsByTagName("html")[0]
                    .classList.remove("is-clipped");
            }
        },        
    },
};
</script>

<style scoped>
.modal-card-body {
    padding: 0px 20px;
}
</style>
