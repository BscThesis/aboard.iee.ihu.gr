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
          <th class="has-text-centered">Διαγραφή</th>
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
          <td class="has-text-centered" style="width: 10%">
            <button class="button is-danger is-light" title="Διαγραφή">
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