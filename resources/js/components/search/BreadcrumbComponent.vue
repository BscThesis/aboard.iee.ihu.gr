<template>
    <section class="hero">
        <div class="hero-body">
            <nav
                class="breadcrumb is-centered is-medium has-bullet-separator"
                aria-label="breadcrumbs"
            >
                <ul>
                    <li>
                        <a href="/">
                            <span class="icon is-small">
                                <i
                                    class="fas fa-bullhorn"
                                    aria-hidden="true"
                                ></i>
                            </span>
                            <span>Ανακοινώσεις</span>
                        </a>
                    </li>
                    <li>
                        <a class="has-text-black has-cursor-auto">
                            <span class="icon is-small">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </span>
                            <span v-if="tag && !author"
                                >Αναζήτηση βάσει επικέτας</span
                            >
                            <span v-if="!tag && author"
                                >Αναζήτηση βάσει καθηγητή</span
                            >
                        </a>
                    </li>
                    <li>
                        <a class="has-text-black has-cursor-auto">
                            <span class="icon is-small">
                                <i
                                    class="fas fa-tag"
                                    v-if="tag && !author"
                                    aria-hidden="true"
                                ></i>
                                <i
                                    class="fas fa-user-tie"
                                    v-if="!tag && author"
                                    aria-hidden="true"
                                ></i>
                            </span>
                            <span>{{ title }}</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        tag: {
            type: Boolean,
            required: false,
            default: false
        },
        author: {
            type: Boolean,
            required: false,
            default: false
        }
    }
};
</script>

<style></style>
