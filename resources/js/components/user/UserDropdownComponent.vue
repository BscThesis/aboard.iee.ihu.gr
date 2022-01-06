<template>
    <div
        class="navbar-item has-dropdown"
        v-bind:class="{ 'is-active': openUserDropdown }"
        v-on-clickaway="away"
    >
        <a
            class="navbar-link is-arrowless is-hidden-touch"
            v-on:click="openUserDropdown = !openUserDropdown"
        >
            <span class="icon">
                <i class="fas fa-user fa-lg"></i>
            </span>
            <span class="icon">
                <i v-if="openUserDropdown" class="fas fa-chevron-up"></i>
                <i v-else class="fas fa-chevron-down"></i>
            </span>
        </a>

        <div class="navbar-dropdown is-radiusless is-right">
            <div class="navbar-item">
                <p class="is-size-6 has-text-weight-bold is-unselectable">
                    {{ user.name }}
                </p>
            </div>
            <div class="navbar-item">
                <p class="is-size-6">{{ user.email }}</p>
            </div>
            <hr class="navbar-divider" />
            <a href="/user/preferences" class="navbar-item">
                <span class="is-size-6">User preferences</span>
            </a>
            <hr class="navbar-divider" />
            <a class="navbar-item" @click="logout()">
                <span class="is-size-6">Logout</span>
            </a>
        </div>
    </div>
</template>

<script>
import { mixin as clickaway } from "vue-clickaway";
//import userMixin from "../mixins/userMixin";

export default {
    mixins: [clickaway],
    props: {
        user: Object,
        required: false
    },
    data: function() {
        return {
            openUserDropdown: false
        };
    },
    methods: {
        away: function() {
            this.openUserDropdown = false;
        },
        logout: function() {
            axios
                .get("/api/auth/logout")
                .then(response => {
                    delete axios.defaults.headers.common["Authorization"];
                    window.location.href = "/";
                })
                .catch(error => {
                    delete axios.defaults.headers.common["Authorization"];
                    window.location.href = "/";
                });
        }
    }
};
</script>

<style></style>
