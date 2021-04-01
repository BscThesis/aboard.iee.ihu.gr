<template>
  <div class="block">
    <nav
      class="pagination is-centered"
      v-bind:class="{ 'is-small': small }"
      role="navigation"
      aria-label="pagination"
    >
      <a
        class="pagination-previous"
        v-bind:disabled="!pagination.prev_page || loadingPrev"
        @click="prevPage()"
        >Προηγούμενη σελίδα</a
      >
      <a
        class="pagination-next"
        v-bind:disabled="!pagination.next_page || loadingNext"
        @click="nextPage()"
        >Επόμενη σελίδα</a
      >
    </nav>
  </div>
</template>

<script>
import { bus } from "../../app";

export default {
  props: {
    small: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data: function () {
    return {
      pagination: {},
      loadingNext: false,
      loadingPrev: false,
    };
  },
  created: function () {
    bus.$on("paginationObject", (data) => {
      this.makePagination(data.links, data.meta);
      this.loadingNext = this.loadingPrev = false;
    });
  },
  methods: {
    makePagination: function (links, meta) {
      let pagination = {
        current_page: meta.current_page,
        last_page: meta.last_page,
        next_page: links.next,
        prev_page: links.prev,
      };
      this.pagination = pagination;
    },
    nextPage: function () {
      if (this.pagination.next_page !== null) {
        this.loadingNext = true;
        bus.$emit("next", this.pagination.next_page);
      }
    },
    prevPage: function () {
      if (this.pagination.prev_page !== null) {
        this.loadingPrev = true;
        bus.$emit("prev", this.pagination.prev_page);
      }
    },
  },
};
</script>