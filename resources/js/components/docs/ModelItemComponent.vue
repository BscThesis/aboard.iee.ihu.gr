<template>
  <div class="card">
    <header class="card-header is-clickable" @click="isOpen = !isOpen">
      <p class="card-header-title">{{ title }}</p>
      <p class="card-header-icon">
        <span class="icon">
          <i
            v-if="isOpen == false"
            class="fas fa-chevron-right"
            aria-hidden="true"
          ></i>
          <i v-else class="fas fa-chevron-down" aria-hidden="true"></i>
        </span>
      </p>
    </header>
    <div class="card-content" v-bind:class="{ 'is-hidden': !isOpen }">
      <div class="content">
        <div class="columns">
          <div class="column is-half">
            <table class="table is-narrow">
              <thead>
                <tr>
                  <th>{</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>}</th>
                </tr>
              </tfoot>
              <tbody>
                <tr v-for="(value, name) in fields" v-bind:key="value + name">
                  <td></td>
                  <td>{{ name }}</td>
                  <td>{{ value }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    fields: {
      type: Object,
      required: false,
    },
  },
  data: function () {
    return {
      isOpen: false,
    };
  },
};
</script>

<style scoped>
.table > tbody > tr > td,
.table > thead > tr > th,
.table > tfoot > tr > th {
  border: 0;
}
.card {
  border-radius: 8px;
}
</style>