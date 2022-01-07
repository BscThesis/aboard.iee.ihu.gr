import Vue from "vue";
import VueCookies from "vue-cookies";

require("./bootstrap");

Vue.use(VueCookies);

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

// Attachments component
Vue.component(
    "attachments",
    require("./components/announcements/single_announcement/full_announcement/AttachmentsComponent.vue")
        .default
);

// Single Announcement Info component
Vue.component(
    "single-announcement-info-component",
    require("./components/announcements/single_announcement/SingleAnnouncementInfoComponent.vue")
        .default
);

// Tags Default component
Vue.component(
    "tags-default-component",
    require("./components/announcements/single_announcement/TagsDefaultComponent.vue")
        .default
);

// Last update component
Vue.component(
    "last-update",
    require("./components/announcements/single_announcement/full_announcement/TimeLastUpdateComponent.vue")
        .default
);

// Full announcement body component
Vue.component(
    "full-announcement-body",
    require("./components/announcements/single_announcement/full_announcement/FullAnnouncementBodyComponent.vue")
        .default
);

// Title for single announcement (full view)
Vue.component(
    "full-announcement-title",
    require("./components/announcements/single_announcement/full_announcement/FullAnnouncementTitleComponent.vue")
        .default
);

// Front page announcement component
Vue.component(
    "single-announcement-component-default",
    require("./components/announcements/single_announcement/SingleAnnouncementComponentDefault.vue")
        .default
);

// Front page announcement title component
Vue.component(
    "single-announcement-title",
    require("./components/announcements/single_announcement/SingleAnnouncementTitleComponent.vue")
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

// Single (full) announcement component
Vue.component(
    "announcement-component-bulma",
    require("./components/announcements/single_announcement/full_announcement/AnnouncementComponent.vue")
        .default
);

// Create announcement component
Vue.component(
    "create-announcement-component-bulma",
    require("./components/announcements/CreateAnnouncementComponent.vue")
        .default
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
    require("./components/announcements/single_announcement/full_announcement/AnnouncementButtonsComponent.vue")
        .default
);

// Back button component
Vue.component(
    "back-button-component",
    require("./components/announcements/single_announcement/full_announcement/BackButtonComponent.vue")
        .default
);

// Edit button component
Vue.component(
    "edit-button-component",
    require("./components/announcements/single_announcement/full_announcement/EditButtonComponent.vue")
        .default
);

// Delete button component
Vue.component(
    "delete-button-component",
    require("./components/announcements/single_announcement/full_announcement/DeleteButtonComponent.vue")
        .default
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

// Sidebar
Vue.component(
    "sidebar",
    require("./components/sidebar/SidebarFilterComponent.vue").default
);

// Search Default
Vue.component(
    "search-default",
    require("./components/sidebar/SearchDefaultComponent.vue").default
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
