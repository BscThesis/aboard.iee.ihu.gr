<template>
  <div class="block">
    <table
      v-if="issues.length >= 0"
      class="table is-fullwidth is-bordered is-hoverable"
    >
      <thead>
        <tr>
          <th class="has-text-centered">Τίτλος</th>
          <th class="has-text-centered">Περιγραφή</th>
          <th class="has-text-centered" style="width: 8%">Διαγραφή</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="issue in issues" v-bind:key="issue.id">
          <td class="has-text-centered" v-bind:title="issue.title">
            {{ issue.title }}
          </td>
          <td class="has-text-centered">
            {{ issue.body }}
          </td>
          <td class="has-text-centered">
            <button
              class="button is-danger is-light"
              title="Διαγραφή"
              v-on:click="deleteIssue(issue.id)"
            >
              <span class="icon is-small">
                <i class="fas fa-trash"></i>
              </span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
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
    getAllIssues: function () {
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
    deleteIssue: function (id) {
      let url = "/api/issues/";
      if (confirm("Do you really want to delete this issue?")) {
        if (url) {
          axios
            .delete(url + id)
            .then(function (response) {
              toast({
                message: "Διεγράφη επιτυχώς",
                type: "is-success",
                position: "bottom-right",
              });
            })
            .catch(function (error) {
              toast({
                message: "Συνέβη κάποιο σφάλμα",
                type: "is-danger",
                position: "bottom-right",
              });
              console.log(error);
            });
        }
      }
    },
  },
};
</script>

<style scoped>
table {
  table-layout: fixed;
}

td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

td:hover {
  white-space: unset;
  overflow: unset;
  text-overflow: unset;
}
</style>