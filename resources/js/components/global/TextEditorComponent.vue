<template>
  <div class="block">
    <div class="field">
      <div class="card">
        <!-- Menubar Content -->
        <header class="card-header has-background-light">
          <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
            <div class="menubar">
              <div class="buttons">
                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Bold"
                  :class="{ 'is-link': isActive.bold() }"
                  @click="commands.bold"
                >
                  <span class="icon is-small">
                    <i class="fas fa-bold"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Italic"
                  :class="{ 'is-link': isActive.italic() }"
                  @click="commands.italic"
                >
                  <span class="icon is-small">
                    <i class="fas fa-italic"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Strikethrough"
                  :class="{ 'is-link': isActive.strike() }"
                  @click="commands.strike"
                >
                  <span class="icon is-small">
                    <i class="fas fa-strikethrough"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Underline"
                  :class="{ 'is-link': isActive.underline() }"
                  @click="commands.underline"
                >
                  <span class="icon is-small">
                    <i class="fas fa-underline"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Code"
                  :class="{ 'is-link': isActive.code() }"
                  @click="commands.code"
                >
                  <span class="icon is-small">
                    <i class="fas fa-code"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Paragraph"
                  :class="{ 'is-link': isActive.paragraph() }"
                  @click="commands.paragraph"
                >
                  <span class="icon is-small">
                    <i class="fas fa-paragraph"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Heading 1"
                  :class="{ 'is-link': isActive.heading({ level: 1 }) }"
                  @click="commands.heading({ level: 1 })"
                >
                  <strong>H1</strong>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Heading 2"
                  :class="{ 'is-link': isActive.heading({ level: 2 }) }"
                  @click="commands.heading({ level: 2 })"
                >
                  <strong>H2</strong>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Heading 3"
                  :class="{ 'is-link': isActive.heading({ level: 3 }) }"
                  @click="commands.heading({ level: 3 })"
                >
                  <strong>H3</strong>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Bullets"
                  :class="{ 'is-link': isActive.bullet_list() }"
                  @click="commands.bullet_list"
                >
                  <span class="icon is-small">
                    <i class="fas fa-list-ul"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Ordered List"
                  :class="{ 'is-link': isActive.ordered_list() }"
                  @click="commands.ordered_list"
                >
                  <span class="icon is-small">
                    <i class="fas fa-list-ol"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Blockquote"
                  :class="{ 'is-link': isActive.blockquote() }"
                  @click="commands.blockquote"
                >
                  <span class="icon is-small">
                    <i class="fas fa-quote-right"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Image"
                  @click="showImagePrompt(commands.image)"
                >
                  <span class="icon is-small">
                    <i class="fas fa-image"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Code Block"
                  :class="{ 'is-link': isActive.code_block() }"
                  @click="commands.code_block"
                >
                  <span class="icon is-small">
                    <i class="fas fa-terminal"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Horizontal Line"
                  @click="commands.horizontal_rule"
                >
                  <span class="icon is-small">
                    <i class="fas fa-minus"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Undo"
                  @click="commands.undo"
                >
                  <span class="icon is-small">
                    <i class="fas fa-undo"></i>
                  </span>
                </button>

                <button
                  class="button is-light is-rounded tooltip"
                  data-tooltip="Redo"
                  @click="commands.redo"
                >
                  <span class="icon is-small">
                    <i class="fas fa-redo"></i>
                  </span>
                </button>
              </div>
            </div>
          </editor-menu-bar>
        </header>

        <!-- Editor -->
        <div class="card-content">
          <editor-content
            class="content has-background-white"
            :editor="editor"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from "tiptap";
import {
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  HorizontalRule,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Strike,
  Underline,
  History,
  Image,
} from "tiptap-extensions";

export default {
  props: ["value"],
  components: {
    EditorContent,
    EditorMenuBar,
  },
  data() {
    return {
      editor: null,
    };
  },
  methods: {
    showImagePrompt(command) {
      let vm = this;
      const src = prompt("Enter the url of your image here");

      // Accept only known image types
      if (src !== null && src.match(/\.(jpeg|jpg|gif|png)$/) != null) {
        command({ src });
      }
    },
  },
  mounted() {
    this.editor = new Editor({
      extensions: [
        new Blockquote(),
        new BulletList(),
        new CodeBlock(),
        new HardBreak(),
        new Heading({ levels: [1, 2, 3] }),
        new HorizontalRule(),
        new ListItem(),
        new OrderedList(),
        new TodoItem(),
        new TodoList(),
        new Link(),
        new Bold(),
        new Code(),
        new Italic(),
        new Strike(),
        new Underline(),
        new History(),
        new Image(),
      ],
      content: this.value,
      onUpdate: ({ getHTML }) => {
        this.$emit("input", getHTML());
      },
    });
  },
  beforeDestroy() {
    if (this.editor) {
      this.editor.destroy();
    }
  },
};
</script>

<style>
.card-header-menubar {
  padding: 0.5rem;
}
</style>