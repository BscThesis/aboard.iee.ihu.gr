<template>
    <div class="block">
        <!-- Page Title -->
        <page-title title="Εκδηλώσεις"></page-title>

        <!-- Content -->
        <div class="block">
            <!-- Events -->
            <div class="columns is-vcentered is-multiline">
                <event-component
                    v-for="event in events.data"
                    v-bind:key="event.id"
                    v-bind:event="event"
                ></event-component>
            </div>

            <!-- Loader -->
            <loader-component></loader-component>

            <!-- Pagination -->
            <pagination
                :data="events"
                :limit="1"
                :show-disabled="true"
                @pagination-change-page="getEvents"
            >
                <span slot="prev-nav">Previous</span>
                <span slot="next-nav">Next</span>
            </pagination>
        </div>
    </div>
</template>

<script>
import { bus } from "../../app";
import { toast } from "bulma-toast";

export default {
    data: function() {
        return {
            events: {}
        };
    },
    created: function() {
        this.getEvents();
    },

    methods: {
        getEvents(page = 1) {
            axios
                .get("/api/events?page=" + page)
                .then(response => {
                    this.events = response.data;
                    bus.$emit("loadingFinished", true);
                })
                .catch(function(error) {
                    bus.$emit("loadingFinished", true);
                    toast({
                        message: "Συνέβη κάποιο σφάλμα",
                        type: "is-danger",
                        position: "bottom-right"
                    });
                });
        }
    }
};
</script>
