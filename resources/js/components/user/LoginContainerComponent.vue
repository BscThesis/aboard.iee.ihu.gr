<template>
  <div class="block">
    <!-- Title -->
    <page-title title="Login"></page-title>

    <!-- Container -->
    <div class="block container">
      <div class="columns is-centered">
        <div class="column is-two-fifths">
          <div class="box">
            <section class="section has-background-ihu"></section>

            <form @submit.prevent="attemptLogin">
              <div class="field">
                <label class="label">Όνομα χρήστη</label>
                <div class="control has-icons-left">
                  <input class="input" required type="input" v-model="input.username" />
                  <span class="icon is-small is-left">
                    <i class="fas fa-user"></i>
                  </span>
                </div>
                <p class="help is-italic">Όνομα χρήστη της μορφής: itXXXXXX</p>
              </div>

              <div class="field">
                <label class="label">Κωδικός</label>
                <div class="control has-icons-left">
                  <input class="input" required type="password" v-model="input.password" />
                  <span class="icon is-small is-left">
                    <i class="fas fa-lock"></i>
                  </span>
                </div>
              </div>

              <div class="field">
                <div class="control">
                  <button class="button is-dark is-fullwidth">Είσοδος</button>
                </div>
              </div>

              <!-- Display errors -->
              <errors-component v-if="errors.length" :errors="errors"></errors-component>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from "bulma-toast";
import { bus } from "../../app";

export default {
  data: function() {
    return {
      input: {
        username: "",
        password: ""
      },
      errors: []
    };
  },
  beforeCreate: function() {
    if (localStorage.getItem("token")) {
      window.location.replace("/");
    }
  },
  methods: {
    attemptLogin: function() {
      let vm = this;
      vm.errors = [];

      if (!vm.input.username) {
        vm.errors.push("Παρακαλώ εισάγετε username");
      }

      if (!vm.input.password) {
        vm.errors.push("Παρακαλώ εισάγετε password");
      }

      if (vm.errors.length == 0) {
        axios
          .post("/api/auth/login", {
            username: vm.input.username,
            password: vm.input.password
          })
          .then(function(response) {
            if (response.status == 200) {
              console.log("response: " + response);
              toast({
                message: "Συνδεθήκατε επιτυχώς",
                type: "is-success",
                position: "bottom-right",
                animate: { in: "fadeIn", out: "fadeOut" }
              });
              localStorage.token = response.data.access_token;
              axios
                .get("/api/auth/user")
                .then(response => {
                  localStorage.setItem(
                    "user_info",
                    JSON.stringify(response.data.data)
                  );
                })
                .catch(error => {
                  localStorage.removeItem("token");
                  localStorage.removeItem("user_info");
                  delete axios.defaults.headers.common["Authorization"];
                  window.location.href = "/login";
                });
              window.location.replace("/");
            } else {
              toast({
                message: response.statusText,
                type: "is-danger",
                position: "bottom-right",
                animate: { in: "fadeIn", out: "fadeOut" }
              });
              console.log(response.data);
            }
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
      }
    }
  }
};
</script>

<style scoped>
.box {
  border-top: 0.5rem solid;
}
.has-background-ihu {
  background-image: url("../../../images/logo.png");
  background-repeat: no-repeat;
  background-position: center;
  margin: 1rem;
}
</style>