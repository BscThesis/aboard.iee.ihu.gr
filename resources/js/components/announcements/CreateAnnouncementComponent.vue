<template>
  <div class="block">
    <!-- Title -->
    <page-title v-if="id" title="Επεξεργασία ανακοίνωσης"></page-title>
    <page-title v-else title="Προσθήκη ανακοίνωσης"></page-title>

    <!-- Creation form -->
    <div class="block" v-if="!loading">
      <!-- Title -->
      <div class="field">
        <label class="label is-capitalized is-unselectable"
          >Τίτλος ανακοίνωσης</label
        >
        <div class="control has-icons-left">
          <input
            class="input"
            type="text"
            placeholder="Τίτλος"
            required
            v-model="announcement.title"
          />
          <span class="icon is-small is-left">
            <i class="fas fa-edit"></i>
          </span>
        </div>
      </div>

      <!-- Body -->
      <div class="field">
        <label class="label is-capitalized is-unselectable">
          Κείμενο ανακοίνωσης
        </label>
        <editor-component v-model="announcement.body"></editor-component>
      </div>

      <!-- English announcement -->
      <div class="field">
        <div class="control">
          <label class="checkbox">
            <input type="checkbox" v-model="announcement.has_eng" />
            Προσθήκη ανακοίνωσης στα αγγλικά
          </label>
        </div>
      </div>

      <div class="field" v-if="announcement.has_eng" id="english-info">
        <!-- Title -->
        <div class="field">
          <label class="label is-capitalized is-unselectable">
            Τίτλος ανακοίνωσης στα αγγλικά
          </label>
          <div class="control has-icons-left">
            <input
              class="input"
              type="text"
              placeholder="Τίτλος"
              required
              v-model="announcement.eng_title"
            />
            <span class="icon is-small is-left">
              <i class="fas fa-edit"></i>
            </span>
          </div>
        </div>

        <!-- Body -->
        <div class="field">
          <label class="label is-capitalized is-unselectable">
            Κείμενο ανακοίνωσης στα αγγλικά
          </label>
          <editor-component v-model="announcement.eng_body"></editor-component>
        </div>
      </div>

      <!-- Other user (admin only) -->
      <div v-if="is_admin" class="field" v-bind:class="{ 'is-hidden': id }">
        <div class="field">
          <div class="columns">
            <div class="column">
              <div class="control">
                <label class="checkbox">
                  <input type="checkbox" v-model="other_user" />
                  Ανέβασμα ως άλλος χρήστης (Admin only)
                </label>
              </div>
            </div>
            <div class="column" v-bind:class="{ 'is-hidden': !other_user }">
              <div class="field" v-if="other_user">
                <label class="label is-capitalized is-unselectable">
                  Συντάκτες
                </label>
                <div
                  class="dropdown"
                  @click="author_open = !author_open"
                  v-bind:class="{ 'is-active': author_open }"
                >
                  <div class="dropdown-trigger">
                    <button
                      class="button"
                      aria-haspopup="true"
                      aria-controls="tag-dropdown"
                    >
                      <span v-if="!selected_author">Authors</span>
                      <span
                        v-if="selected_author"
                        @click="selected_author = null"
                        class="icon is-small has-text-danger"
                        title="Remove"
                      >
                        <i class="fas fa-times"></i>
                      </span>
                      <span v-if="selected_author">
                        {{ selected_author.name }}
                      </span>
                      <span class="icon is-small">
                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                      </span>
                    </button>
                  </div>
                  <div class="dropdown-menu" id="tag-dropdown" role="menu">
                    <div class="dropdown-content dropdown-content-fixed">
                      <a
                        class="dropdown-item"
                        v-for="author in authors"
                        v-bind:key="author.id"
                        @click="selected_author = author"
                      >
                        {{ author.name }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tags -->
      <div class="field">
        <div class="columns">
          <!-- Tag selector -->
          <div class="column">
            <label class="label is-capitalized is-unselectable">
              Επιλογή ετικετών (Work in progress)
            </label>
            <!-- <div
              class="dropdown"
              @click="dropAct = !dropAct"
              v-bind:class="{ 'is-active': dropAct }"
            >
              <div class="dropdown-trigger">
                <button
                  class="button"
                  aria-haspopup="true"
                  aria-controls="tag-dropdown"
                >
                  <span
                    >Έχουν επιλεγεί: ( {{ announcement.tags.length }} )</span
                  >
                  <span class="icon is-small">
                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                  </span>
                </button>
              </div>
              <div class="dropdown-menu" id="tag-dropdown" role="menu">
                <div class="dropdown-content dropdown-content-fixed">
                  <div
                    class="dropdown-item"
                    v-for="tag in allTags"
                    v-bind:key="tag.id"
                  >
                    <label class="checkbox">
                      <input
                        type="checkbox"
                        v-bind:value="tag.id"
                        v-model="announcement.tags"
                      />
                      {{ tag.title }}
                    </label>
                  </div>
                </div>
              </div>
            </div> -->
            <tags-tree v-if="tagsAsTree" :treeData="tagsAsTree"></tags-tree>
          </div>
          <!-- Tag view (parent child) -->
          <div class="column">
            <label class="label is-capitalized is-unselectable">
              Επιλεγμένες ετικέτες (auto-inserted)
            </label>
            <div class="tags">
              <span
                class="tag is-rounded is-dark is-unselectable"
                v-for="tag in selectedTags"
                v-bind:key="tag"
              >
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pinned -->
      <div class="field">
        <div class="columns">
          <div class="column">
            <div class="control">
              <label class="checkbox">
                <input type="checkbox" v-model="announcement.is_pinned" />
                Προσθήκη ανακοίνωσης στις σημαντικές
              </label>
            </div>
          </div>
          <div
            class="column"
            v-bind:class="{ 'is-hidden': !announcement.is_pinned }"
          >
            <div class="field" id="pinned-until">
              <div class="field">
                <label class="label is-capitalized is-unselectable">
                  Εμφάνιση μέχρι
                </label>
                <flat-pickr
                  v-model="announcement.pinned_until"
                  :config="config"
                ></flat-pickr>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Attachments -->
      <div class="block">
        <div class="columns">
          <!-- File input -->
          <div class="column">
            <label class="label is-capitalized is-unselectable">
              Ανέβασμα αρχείων
            </label>
            <div class="file">
              <label class="file-label">
                <input
                  class="file-input"
                  type="file"
                  name="file"
                  @change="onFileChanged($event)"
                />
                <span class="file-cta has-background-white">
                  <span class="file-icon">
                    <i class="fas fa-upload"></i>
                  </span>
                  <span class="file-label">Επιλογή αρχείου...</span>
                </span>
              </label>
            </div>
          </div>

          <!-- File list -->
          <div class="column">
            <label class="label is-capitalized is-unselectable"
              >Λίστα αρχείων</label
            >
            <div class="field is-grouped is-grouped-multiline">
              <div
                class="control"
                v-for="(item, index) in announcement.attachments"
                v-bind:key="index"
              >
                <div class="tags has-addons">
                  <a v-if="item.filename" class="tag is-info is-light">{{
                    item.filename
                  }}</a>
                  <a v-else class="tag is-info is-light">{{ item.name }}</a>
                  <a
                    class="tag is-delete"
                    v-on:click="removeFromFileUpload(index)"
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Event -->
      <div class="field">
        <div class="control">
          <label class="checkbox">
            <input type="checkbox" v-model="announcement.is_event" /> Προσθήκη
            εκδήλωσης
          </label>
        </div>
      </div>

      <!-- Event information -->
      <div class="field" id="event-related-info" v-if="announcement.is_event">
        <div class="field">
          <label class="label is-capitalized is-unselectable">
            Τοποθεσία εκδήλωσης
          </label>
          <div class="control has-icons-left">
            <input
              class="input"
              type="text"
              placeholder="Τοποθεσία"
              required
              v-model="announcement.event_location"
            />
            <span class="icon is-small is-left">
              <i class="fas fa-edit"></i>
            </span>
          </div>

          <!-- Google Maps -->
          <div class="control">
            <label class="checkbox">
              <input type="checkbox" v-model="announcement.gmaps" />
              Προσθήκη link στο Google Maps
            </label>
          </div>
        </div>

        <div class="field">
          <div class="columns">
            <div class="column">
              <label class="label is-capitalized is-unselectable">
                Έναρξη εκδήλωσης
              </label>
              <flat-pickr
                v-model="announcement.event_start_time"
                :config="config"
              >
              </flat-pickr>
            </div>
            <div class="column">
              <label class="label is-capitalized is-unselectable">
                Λήξη εκδήλωσης
              </label>
              <flat-pickr
                v-model="announcement.event_end_time"
                :config="config"
              ></flat-pickr>
            </div>
          </div>
        </div>
      </div>

      <div class="field" v-if="has_public">
        <article class="message is-danger">
          <div class="message-body">
            <p class="subtitle is-6">Η ανακοίνωση περιέχει δημόσιες ετικέτες</p>
          </div>
        </article>
      </div>

      <!-- Submit button -->
      <div class="field">
        <a
          class="button is-link is-capitalized"
          @click="addAnnouncement()"
          v-bind:class="{ 'is-loading': btnLoading }"
          v-bind:disabled="btnLoading"
        >
          Αποθήκευση
        </a>
        <a class="button is-link is-capitalized" @click="showTagsAsTree()">
          showTagsAsTree
        </a>
      </div>

      <!-- Display errors -->
      <errors-component v-if="errors.length" :errors="errors">
      </errors-component>
    </div>
  </div>
</template>

<script>
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { toast } from "bulma-toast";
import { bus } from "../../app";

export default {
  props: {
    id: {
      type: Number,
      required: false,
    },
  },
  components: {
    flatPickr,
  },
  data: function () {
    return {
      loading: true,
      // Single announcement
      announcement: {
        title: "",
        body: "",
        is_pinned: false,
        pinned_until: null,
        tags: [],
        is_event: false,
        event_location: "",
        event_start_time: null,
        event_end_time: null,
        gmaps: false,
        has_eng: false,
        eng_title: null,
        eng_body: null,
        attachments: [],
      },
      // Admin add as other author
      is_admin: false,
      other_user: false,
      selected_author: null,
      authors: [],
      author_open: false,
      // All tags (for dropdown)
      tags: [],
      // dropdown active
      dropAct: false,
      // change submit button state
      btnLoading: false,
      // event related vars
      config: {
        altInput: true,
        enableTime: true,
        defaultHour: 0,
        minuteIncrement: 1,
        time_24hr: true,
        // minDate: new Date().fp_incr(1),
        dateFormat: "Y-m-d H:i",
      },
      // errors
      errors: [],
      // announcement has public tag
      has_public: false,
    };
  },
  created: function () {
    this.getAllTags();
    bus.$on("authCheckFinished", () => {
      if (localStorage.getItem("user_info")) {
        let vm = this;
        vm.is_admin = JSON.parse(localStorage.getItem("user_info")).is_admin;
        if (vm.is_admin) {
          this.getAuthors();
        }
      }
    });
  },
  mounted: function () {
    if (this.id) {
      this.getSingle();
    } else {
      this.loading = false;
    }
  },
  methods: {
    showTagsAsTree: function () {
      let vm = this;
      console.log(JSON.stringify(vm.tagsAsTree, null, " "));
    },
    getAuthors: function () {
      let vm = this;
      axios
        .get("/api/auth/authors")
        .then(function (response) {
          vm.authors = response.data;
        })
        .catch(function (error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" },
          });
          console.log(error);
        });
    },
    getSingle: function () {
      let vm = this;
      axios
        .get("/api/announcements/" + vm.id)
        .then(function (response) {
          vm.announcement = response.data.data;
          vm.tagObjectsToArray();
          vm.loading = false;
        })
        .catch(function (error) {
          vm.loading = false;
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" },
          });
          console.log(error);
        });
    },
    getAllTags: function () {
      let vm = this;
      axios
        .get("/api/tags")
        .then(function (response) {
          vm.tags = response.data.data;
        })
        .catch(function (error) {
          toast({
            message: "Συνέβη κάποιο σφάλμα",
            type: "is-danger",
            position: "bottom-right",
            animate: { in: "fadeIn", out: "fadeOut" },
          });
          console.log(error);
        });
    },
    addAnnouncement: function () {
      let vm = this;
      vm.errors = [];
      this.btnLoading = true;
      let url = "";

      // Create new form data for submission
      let formData = new FormData();

      // Add files first
      if (vm.announcement.attachments.length > 0) {
        let old_attachments = vm.announcement.attachments.filter(function (
          attachment
        ) {
          return attachment.id;
        });

        let new_attachments = vm.announcement.attachments.filter(function (
          attachment
        ) {
          return !attachment.id;
        });

        if (old_attachments.length > 0) {
          formData.append("attachments_old", JSON.stringify(old_attachments));
        }

        if (new_attachments.length > 0) {
          for (var i = 0; i < new_attachments.length; i++) {
            let file = new_attachments[i];
            formData.append("attachments[" + i + "]", file);
          }
        }
      }

      if (!vm.announcement.title) {
        vm.errors.push("Ο τίτλος της ανακοίνωσης δεν μπορεί να είναι κενός");
      } else {
        formData.append("title", vm.announcement.title);
      }

      if (
        !vm.announcement.body ||
        vm.announcement.body.replace(/<[^>]*>?/gm, "") == ""
      ) {
        vm.errors.push("Το κείμενο της ανακοίνωσης δεν μπορεί να είναι κενό");
      } else {
        formData.append("body", vm.announcement.body);
      }

      formData.append("is_pinned", JSON.stringify(vm.announcement.is_pinned));

      // Conditionally add pinned until
      if (vm.announcement.is_pinned) {
        if (!vm.announcement.pinned_until) {
          vm.errors.push(
            "Μία καρφιτσωμένη ανακοίνωση πρέπει να έχει ημερομηνία λήξης"
          );
        } else {
          formData.append(
            "pinned_until",
            JSON.stringify(vm.announcement.pinned_until)
          );
        }
      }

      formData.append("is_event", JSON.stringify(vm.announcement.is_event));

      // Conditionally add event related info
      // Necessary because one can have the fields filled,
      // but is_event not checked
      if (vm.announcement.is_event) {
        if (!vm.announcement.event_location) {
          vm.errors.push("Ο τόπος της εκδήλωσης δεν μπορεί να είναι κενός");
        } else {
          formData.append("event_location", vm.announcement.event_location);
        }

        formData.append(
          "event_start_time",
          JSON.stringify(vm.announcement.event_start_time)
        );
        formData.append(
          "event_end_time",
          JSON.stringify(vm.announcement.event_end_time)
        );
        formData.append("gmaps", JSON.stringify(vm.announcement.gmaps));
      }

      if (vm.announcement.tags.length == 0) {
        vm.errors.push("Η ανακοίνωση πρέπει να έχει τουλάχιστον μία ετικέτα");
      } else {
        formData.append("tags", JSON.stringify(vm.announcement.tags));
      }

      if (vm.other_user) {
        if (vm.selected_author) {
          formData.append("user_id", vm.selected_author.id);
        }
      }

      formData.append("has_eng", JSON.stringify(vm.announcement.has_eng));

      // Same for english
      if (vm.announcement.has_eng) {
        if (!vm.announcement.eng_title) {
          vm.errors.push(
            "Ο τίτλος της ανακοίνωσης στα αγγλικά δεν μπορεί να είναι κενός"
          );
        } else {
          formData.append("eng_title", vm.announcement.eng_title);
        }

        if (
          !vm.announcement.eng_body ||
          vm.announcement.eng_body.replace(/<[^>]*>?/gm, "") == ""
        ) {
          vm.errors.push(
            "Το κείμενο της ανακοίνωσης στα αγγλικά δεν μπορεί να είναι κενό"
          );
        } else {
          formData.append("eng_body", vm.announcement.eng_body);
        }
      }

      if (vm.id) {
        formData.append("_method", "put");
        url = "/api/announcements/" + vm.id;
      } else {
        url = "/api/announcements";
      }

      if (vm.errors.length == 0) {
        axios
          .post(url, formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then(function (response) {
            vm.btnLoading = false;
            toast({
              message: "Προστέθηκε επιτυχώς",
              type: "is-success",
              position: "bottom-right",
              animate: { in: "fadeIn", out: "fadeOut" },
            });
            window.location.replace("/announcements/" + response.data.data.id);
          })
          .catch(function (error) {
            vm.btnLoading = false;
            toast({
              message: "Συνέβη κάποιο σφάλμα",
              type: "is-danger",
              position: "bottom-right",
              animate: { in: "fadeIn", out: "fadeOut" },
            });
            console.log(error.response.data);
          });
      } else {
        vm.btnLoading = false;
      }
    },
    onFileChanged: function (event) {
      let vm = this;
      if (!vm.announcement.attachments.includes(event.target.files[0])) {
        vm.announcement.attachments.push(event.target.files[0]);
      }
      vm.singleFile = null;
    },
    removeFromFileUpload: function (index) {
      let vm = this;
      vm.$delete(vm.announcement.attachments, index);
    },
    tagObjectsToArray: function () {
      // This function converts the tag objects array returned by the api
      // to a number only array expected by the api on put
      let vm = this;
      let tagArray = [];

      if (vm.announcement.tags.length > 0) {
        vm.announcement.tags.forEach((element) => {
          tagArray.push(element.id);
        });
      }
      vm.announcement.tags = tagArray;
    },
  },
  computed: {
    selectedTags: function () {
      let selected = new Set();
      let vm = this;
      let parent;
      vm.has_public = false;

      if (vm.tags.length > 0) {
        vm.announcement.tags.forEach((element) => {
          var tag = vm.tags.find(function (obj) {
            return obj.id === element || obj.id === element.id;
          });

          selected.add(tag.title);
          parent = tag.parent_id;

          if (tag.is_public) {
            vm.has_public = true;
          }

          while (parent != null) {
            var tag = vm.tags.find(function (obj) {
              return obj.id === parent;
            });

            if (tag.is_public) {
              vm.has_public = true;
            }

            if (tag.parent_id != null) {
              selected.add(tag.title);
            }
            parent = tag.parent_id;
          }
        });
      }

      return selected;
    },
    allTags: function () {
      let vm = this;
      return vm.tags.filter(function (el) {
        return el.parent_id != null;
      });
    },
    tagsAsTree: function () {
      var ID_KEY = "id";
      var PARENT_KEY = "parent_id";
      var CHILDREN_KEY = "children";
      var DEPTH = "depth";

      let vm = this;

      var tree = [],
        childrenOf = {};
      var item, id, parentId;

      for (var i = 0, length = vm.tags.length; i < length; i++) {
        item = vm.tags[i];
        id = item[ID_KEY];
        parentId = item[PARENT_KEY] || 0;
        childrenOf[id] = childrenOf[id] || [];
        item[CHILDREN_KEY] = childrenOf[id];
        item[DEPTH] = item[DEPTH] || 0;
        if (parentId != 0) {
          childrenOf[parentId] = childrenOf[parentId] || [];
          item[DEPTH] += 1;
          childrenOf[parentId].push(item);
        } else {
          tree.push(item);
        }
      }
      return tree[0];
    },
  },
};
</script>