<template>
  <div class="block">
    <!-- Page Title -->
    <page-title title="Συντάκτες"></page-title>

    <!-- Content -->
    <div class="block">
      <div class="columns is-multiline is-vcentered">
        <div class="column is-one-third" v-for="author in authors" v-bind:key="author.id">
          <div class="box" @click="searchByAuthor(author.id)">
            <div class="columns is-mobile is-vcentered">
              <div class="column is-10">
                <h1 class="title is-4 is-unselectable">{{ author.name }}</h1>
              </div>
              <div class="column">
                <span class="icon">
                  <i class="fas fa-lg fa-chevron-right"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      authors: []
    };
  },
  created: function() {
    this.getAuthors();
  },
  methods: {
    getAuthors: function() {
      let vm = this;
      axios
        .get("/api/auth/authors")
        .then(function(response) {
          vm.authors = response.data;
        })
        .catch(function(error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" }
          });
          console.log(error);
        });
    },
    searchByAuthor: function(id) {
      window.location.href = "/search/author/" + id;
    }
  }
};
</script>

<style scoped>
.box:hover {
  cursor: pointer;
  color: #1a6dae;
}
</style>