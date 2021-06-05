<template>
  <div class="block">
    <section class="section">
      <div class="tabs is-toggle is-centered">
        <ul>
          <li
            v-bind:class="{ 'is-active': isActive == 1 }"
            v-on:click="setActiveComponent('admin-tags-component-bulma', 1)"
          >
            <a>
              <span class="icon is-small">
                <i class="fas fa-tag" aria-hidden="true"></i>
              </span>
              <span>Tags</span>
            </a>
          </li>
          <li
            v-bind:class="{ 'is-active': isActive == 2 }"
            v-on:click="setActiveComponent('dummy-componenent', 2)"
          >
            <a>
              <span class="icon is-small">
                <i class="fas fa-image" aria-hidden="true"></i>
              </span>
              <span>Dummy</span>
            </a>
          </li>
        </ul>
      </div>
    </section>

    <div class="block">
      <component v-bind:is="currentComponent"></component>
    </div>
  </div>
</template>

<script>
import tagsComp from "./AdminTagsComponent.vue";
import dummyComp from "../ExampleComponent.vue";

export default {
  components: {
    "admin-tags-component-bulma": tagsComp,
    "dummy-componenent": dummyComp,
  },
  data() {
    return { currentComponent: "admin-tags-component-bulma", isActive: 1 };
  },
  methods: {
    alert(v) {
      alert(v);
    },
    setActiveComponent(comp, num) {
      this.currentComponent = comp;
      this.isActive = num;
    },
  },
};
</script>
