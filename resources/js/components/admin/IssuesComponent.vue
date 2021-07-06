<template>
  <div class="block"></div>
</template>

<script>
export default {
  data: function () {
    return {
      issues: {},
    };
  },
  created: function () {
    this.getAllIssues();
  },
  methods: {
    getAllIssues() {
      let vm = this;
      axios
        .get("/api/issues")
        .then(function (response) {
          vm.issues = response.data.data;
        })
        .catch(function (error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
          });
          console.log(error);
        });
    },
  },
};
</script>

<style>
</style>