<template>
  <div>
    <button class="button is-danger is-uppercase" @click="modalOpen=true">Admin area</button>

    <!-- <div class="modal">
      <div class="modal-background" </div>
      <div class="modal-content">
        <div class="block"></div>
      </div>
      <button class="modal-close is-large" @click="modalOpen=false" aria-label="close"></button>
    </div>-->

    <div class="modal" v-bind:class="{ 'is-active': modalOpen }">
      <div class="modal-background" @click="modalOpen=false">></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Διαχείριση ετικετών</p>
          <button class="delete" aria-label="close" @click="modalOpen=false"></button>
        </header>
        <section class="modal-card-body">
          <admin-component></admin-component>
        </section>
        <footer class="modal-card-foot">
          <button class="button" @click="modalOpen=false">Exit</button>
        </footer>
      </div>
    </div>
    <!--  -->
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      modalOpen: false
    };
  }
};
</script>

<style>
</style>