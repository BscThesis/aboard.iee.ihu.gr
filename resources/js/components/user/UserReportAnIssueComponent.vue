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
          v-model="issue.title"
        ></textarea>
      </div>
    </div>

    <div class="field">
      <div class="control">
        <button class="button is-link">Υποβολή</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data: function () {
    return {
      issue: {},
    };
  },
};
</script>

<style>
</style>