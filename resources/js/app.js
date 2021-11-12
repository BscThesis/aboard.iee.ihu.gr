require("./bootstrap");

window.Vue = require("vue");

//////////////////////////////////////////////////////////////////////////////////////////////////////
// Page title
Vue.component(
    "page-title",
    require("./components/global/PageTitleComponent.vue").default
);

// Links for front page
Vue.component(
    "front-page-links",
    require("./components/global/FrontPageLinksComponent.vue").default
);

// Tags component
Vue.component(
    "tags",
    require("./components/announcements/TagsComponent.vue").default
);

// Tags tree component
Vue.component(
    "tags-tree",
    require("./components/announcements/TagsTreeComponent.vue").default
);

// Node component
Vue.component(
    "node-tree",
    require("./components/announcements/NodeTreeComponent.vue").default
);

// Tags caret component
Vue.component(
    "tag-caret",
    require("./components/announcements/TagCaretComponent.vue").default
);

// Tags tree component
Vue.component(
    "tag-click",
    require("./components/announcements/TagClickComponent.vue").default
);

// Attachments component
Vue.component(
    "attachments",
    require("./components/announcements/AttachmentsComponent.vue").default
);

// Single Announcement Info component
Vue.component(
    "single-announcement-info-component",
    require("./components/announcements/SingleAnnouncementInfoComponent.vue")
        .default
);

// Tags Default component
Vue.component(
    "tags-default-component",
    require("./components/announcements/TagsDefaultComponent.vue").default
);

// Last update component
Vue.component(
    "last-update",
    require("./components/announcements/TimeLastUpdateComponent.vue").default
);

// Full announcement body component
Vue.component(
    "full-announcement-body",
    require("./components/announcements/FullAnnouncementBodyComponent.vue")
        .default
);

// Title for single announcement (full view)
Vue.component(
    "full-announcement-title",
    require("./components/announcements/FullAnnouncementTitleComponent.vue")
        .default
);

// Front page announcement component
Vue.component(
    "single-announcement-component-default",
    require("./components/announcements/SingleAnnouncementComponentDefault.vue")
        .default
);

// Front page announcement component
Vue.component(
    "single-announcement-component",
    require("./components/announcements/SingleAnnouncementComponent.vue")
        .default
);

// Front page announcement title component
Vue.component(
    "single-announcement-title",
    require("./components/announcements/SingleAnnouncementTitleComponent.vue")
        .default
);

// Navbar component
Vue.component(
    "navbar-component-bulma",
    require("./components/global/NavbarComponent.vue").default
);

// Footer component
Vue.component(
    "footer-component",
    require("./components/global/FooterComponent.vue").default
);

// All announcements component
Vue.component(
    "announcements-component-bulma",
    require("./components/announcements/AnnouncementsComponent.vue").default
);

// WYSIWYG editor component
Vue.component(
    "editor-component",
    require("./components/global/TextEditorComponent.vue").default
);

// All events component
Vue.component(
    "events-component-bulma",
    require("./components/events/EventsComponent.vue").default
);

// Single event component
Vue.component(
    "event-component",
    require("./components/events/SingleEventComponent.vue").default
);

// Single (full) announcement component
Vue.component(
    "announcement-component-bulma",
    require("./components/announcements/AnnouncementComponent.vue").default
);

// Create announcement component
Vue.component(
    "create-announcement-component-bulma",
    require("./components/announcements/CreateAnnouncementComponent.vue")
        .default
);

// Search tag component
Vue.component(
    "search-bytag-component-bulma",
    require("./components/search/SearchByTagComponent.vue").default
);

// Search author component
Vue.component(
    "search-byauthor-component-bulma",
    require("./components/search/SearchByAuthorComponent.vue").default
);

// Custom search component
Vue.component(
    "custom-search-component",
    require("./components/search/CustomSearchComponent.vue").default
);

// Search component for front page
Vue.component(
    "search-component",
    require("./components/search/SearchComponent.vue").default
);

// Breadcrumb component
Vue.component(
    "breadcrumb-component",
    require("./components/search/BreadcrumbComponent.vue").default
);

// Authors component
Vue.component(
    "authors-component",
    require("./components/global/AuthorsComponent.vue").default
);

// Tags component
Vue.component(
    "tags-component",
    require("./components/global/TagsComponent.vue").default
);

// Loader component
Vue.component(
    "loader-component",
    require("./components/global/LoaderComponent.vue").default
);

// Admin dashboard component
Vue.component(
    "admin-component",
    require("./components/admin/TagsComponent.vue").default
);

// Admin view issues component
Vue.component(
    "issues-component",
    require("./components/admin/IssuesComponent.vue").default
);

// Admin dashboard component
Vue.component(
    "admin-modal",
    require("./components/admin/ModalComponent.vue").default
);

// Add tag component
Vue.component(
    "add-tag-component",
    require("./components/admin/AddTagComponent.vue").default
);

// Errors component
Vue.component(
    "errors-component",
    require("./components/errors/ErrorsComponent.vue").default
);

// Delete component
Vue.component(
    "delete-modal-component",
    require("./components/global/DeleteModalComponent.vue").default
);

// Announcement buttons component
Vue.component(
    "announcement-buttons-component",
    require("./components/announcements/AnnouncementButtonsComponent.vue")
        .default
);

// Back button component
Vue.component(
    "back-button-component",
    require("./components/announcements/BackButtonComponent.vue").default
);

// Edit button component
Vue.component(
    "edit-button-component",
    require("./components/announcements/EditButtonComponent.vue").default
);

// Delete button component
Vue.component(
    "delete-button-component",
    require("./components/announcements/DeleteButtonComponent.vue").default
);

// User dropdown
Vue.component(
    "user-dropdown-component",
    require("./components/user/UserDropdownComponent.vue").default
);

// User activity
Vue.component(
    "user-activity-component",
    require("./components/user/UserActivityComponent.vue").default
);

// Error
Vue.component(
    "error-component",
    require("./components/errors/ErrorComponent.vue").default
);

// Docs item
Vue.component(
    "doc-item",
    require("./components/docs/DocItemComponent.vue").default
);

// Docs container
Vue.component(
    "docs-list",
    require("./components/docs/DocsContainerComponent.vue").default
);

// Doc model item
Vue.component(
    "model-item",
    require("./components/docs/ModelItemComponent.vue").default
);

// Login container
Vue.component(
    "login-container",
    require("./components/user/LoginContainerComponent.vue").default
);

// User preferences
Vue.component(
    "user-preferences",
    require("./components/user/UserPreferencesComponent.vue").default
);

// Laravel Vue Pagination
Vue.component(
    "pagination",
    require("./components/pagination/LaravelVuePagination.vue").default
);

Vue.component(
    "renderless-laravel-vue-pagination",
    require("./components/pagination/RenderlessLaravelVuePagination.vue")
        .default
);

/////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Global event bus
export const bus = new Vue();

const app = new Vue({
    el: "#app"
});
