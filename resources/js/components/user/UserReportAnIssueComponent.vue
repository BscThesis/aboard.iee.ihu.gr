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
        <button
          class="button is-link"
          v-on:click="submitAnIssue()"
          v-bind:class="{ 'is-loading': btnLoading }"
          v-bind:disabled="btnLoading"
        >
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
      issue: {
        title: "",
        body: "",
      },
      errors: [],
      btnLoading: false,
    };
  },
  methods: {
    submitAnIssue: function () {
      let vm = this;
      vm.errors = [];
      if (!vm.issue.title) {
        vm.errors.push("Ο τίτλος δεν μπορεί να είναι κενός");
      }
      if (!vm.issue.body.trim() || !vm.issue.body) {
        vm.errors.push("Το κείμενο δεν μπορεί να είναι κενό");
      }
      if (vm.issue.body) {
        if (!vm.issue.body.trim()) {
          vm.errors.push("Το κείμενο δεν μπορεί να είναι κενό");
        }
      }

      if (vm.errors.length == 0) {
        vm.btnLoading = true;
        axios
          .post("/api/issues", {
            title: this.issue.title,
            body: this.issue.body,
          })
          .then(function (response) {
            toast({
              message: "Αποθηκεύτηκε επιτυχώς",
              type: "is-success",
              position: "bottom-right",
              animate: { in: "fadeIn", out: "fadeOut" },
            });
            vm.btnLoading = false;
          })
          .catch(function (error) {
            toast({
              message: "Συνέβη κάποιο σφάλμα",
              type: "is-danger",
              position: "bottom-right",
              animate: { in: "fadeIn", out: "fadeOut" },
            });
            console.log(error.response.data);
            vm.btnLoading = false;
          });
      }
    },
  },
};
</script>

<style>
</style>