<template>
  <div class="block">
    <div class="field">
      <label class="label">Τίτλος</label>
      <div class="control">
        <input
          class="input"
          type="text"
          placeholder="Τίτλος"
          v-model="issue.title"
        />
      </div>
    </div>

    <div class="field">
      <label class="label">Κείμενο</label>
      <div class="control">
        <textarea
          class="textarea"
          placeholder="Κείμενο"
          v-model="issue.body"
        ></textarea>
      </div>
    </div>

    <div class="field">
      <div class="control">
        <button class="button is-link" v-on:click="submitAnIssue()">
          Υποβολή
        </button>
      </div>
    </div>

    <errors-component v-if="errors.length" :errors="errors"> </errors-component>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      issue: {},
      errors: [],
    };
  },
  methods: {
    submitAnIssue: function () {
      let vm = this;
      if (!vm.issue.title) {
        vm.errors.push("Ο τίτλος δεν μπορεί να είναι κενός");
      }
      if (!vm.issue.body || vm.issue.body.replace(/<[^>]*>?/gm, "") == "") {
        vm.errors.push("Το κείμενο δεν μπορεί να είναι κενό");
      }
    },
  },
};
</script>

<style>
</style>