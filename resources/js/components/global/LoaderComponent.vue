<template>
  <section class="hero is-medium" v-if="loading == true">
    <div class="hero-body">
      <div class="container has-text-centered">
        <div class="loader">Loading...</div>
      </div>
    </div>
  </section>
</template>

<script>
import { bus } from "../../app";

export default {
  data: function () {
    return {
      loading: true,
    };
  },
  created: function () {
    bus.$on("loadingFinished", (data) => {
      if (data) {
        this.loading = false;
      }
    });
  },
};
</script>

<style scoped>
.loader,
.loader:after {
  border-radius: 50%;
  width: 10em;
  height: 10em;
}
.loader {
  margin: 60px auto;
  font-size: 10px;
  position: relative;
  text-indent: -9999em;
  border-top: 1.1em solid #dbdbdb;
  border-right: 1.1em solid #dbdbdb;
  border-bottom: 1.1em solid #dbdbdb;
  border-left: 1.1em solid #383838;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation: load8 1.1s infinite linear;
  animation: load8 1.1s infinite linear;
}
@-webkit-keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes load8 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>