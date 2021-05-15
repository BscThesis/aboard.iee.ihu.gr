<template>
  <div class="block">
    <!-- Page title -->
    <page-title title="Προτιμήσεις χρήστη"></page-title>

    <!-- Tab navigation -->
    <div class="tabs">
      <ul>
        <li v-bind:class="{ 'is-active': component == 'activityTable' }">
          <a @click="component = 'activityTable'">
            <span class="is-uppercase has-text-weight-medium">Activity</span>
          </a>
        </li>
        <li v-bind:class="{ 'is-active': component == 'subscribeBoards' }">
          <a @click="component = 'subscribeBoards'">
            <span class="is-uppercase has-text-weight-medium">Subscribe</span>
          </a>
        </li>
        <li v-bind:class="{ 'is-active': component == 'reportAnIssue' }">
          <a @click="component = 'reportAnIssue'">
            <span class="is-uppercase has-text-weight-medium"
              >Report an Issue</span
            >
          </a>
        </li>
      </ul>
    </div>

    <!-- Content -->
    <div class="block">
      <component v-bind:is="component"></component>
    </div>
  </div>
</template>

<script>
import UserActivityTableComponent from "./UserActivityTableComponent.vue";
import UserSubscribeToBoardsComponent from "./UserSubscribeToBoardsComponent.vue";
import UserSubscribeToBoardsComponent from "./UserReportAnIssueComponent.vue";

export default {
  components: {
    activityTable: UserActivityTableComponent,
    subscribeBoards: UserSubscribeToBoardsComponent,
    reportAnIssue: UserReportAnIssueComponent,
  },
  data: function () {
    return {
      component: "activityTable",
    };
  },
  methods: {},
};
</script>