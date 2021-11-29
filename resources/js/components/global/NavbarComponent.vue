<template>
    <nav
        class="navbar has-shadow is-fixed-top is-spaced"
        role="navigation"
        aria-label="main navigation"
    >
        <div class="navbar-brand">
            <a class="navbar-item" href="/">
                <p class="is-size-5 has-text-link">
                    <span class="icon">
                        <i class="fas fa-bullhorn"></i>
                    </span>
                    <span>Ανακοινώσεις</span>
                </p>
            </a>

            <a
                role="button"
                class="navbar-burger burger"
                v-bind:class="{ 'is-active': showMobileMenu }"
                v-on:click="showMobileMenu = !showMobileMenu"
                aria-label="menu"
                aria-expanded="false"
                data-target="defaultNavbar"
            >
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div
            id="defaultNavbar"
            class="navbar-menu"
            v-bind:class="{ 'is-active': showMobileMenu }"
        >
            <div class="navbar-start">
                <span class="navbar-divider is-hidden-touch"></span>
                <a class="navbar-item" href="/">Αρχική</a>
            </div>

            <div class="navbar-end">
                <user-activity-component
                    v-if="userAuthenticated"
                ></user-activity-component>
                <span class="navbar-divider" v-if="userAuthenticated"></span>
                <user-dropdown-component
                    v-if="userAuthenticated"
                ></user-dropdown-component>
                <div v-if="!userAuthenticated" class="navbar-item">
                    <a class="button is-dark" href="/login">
                        <span class="is-size-6">Είσοδος</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import userMixin from "../mixins/userMixin";

export default {
    mixins: [userMixin],
    data: function() {
        return {
            showMobileMenu: false
        };
    }
};
</script>

<style scoped>
.navbar-divider {
    height: 32px;
    border-right: 1px solid #e8e8e8;
}
</style>
