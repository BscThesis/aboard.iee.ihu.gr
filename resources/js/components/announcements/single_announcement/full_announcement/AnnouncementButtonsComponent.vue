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
export default {
    props: {
        announcement: {
            type: Object,
            required: true
        },
	user: {
	    type: Object,
	    required: false
	}
    },
    data: function() {
        return {
            is_admin: false,
            is_the_author: false
        };
    },
    watch: {
	user: {
	    immediate: true,
	    handler (val, oldVal) {
		if (val) {
		    this.is_admin = val.is_admin;
		    this.is_the_author = val.id == this.announcement.author.id;
		}
	    }
	}
    }
};
</script>
