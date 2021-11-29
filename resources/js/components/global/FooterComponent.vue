<template>
    <footer class="footer container">
        <div class="container">
            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="level-item subtitle is-6">Σύστημα ανακοινώσεων</p>
                </div>

                <p class="level-item has-text-centered">
                    <a title="Επικοινωνία" @click="bodyClass(true)">
                        <span class="icon is-medium">
                            <i class="fas fa-2x fa-envelope"></i>
                        </span>
                    </a>
                </p>

                <div class="level-right is-hidden-mobile">
                    <p
                        v-if="is_admin || is_author"
                        class="level-item is-size-7"
                    >
                        <a href="/documentation">API Docs</a>
                    </p>
                    <p v-if="is_admin" class="level-item is-size-7">
                        <a
                            href="https://github.com/BscThesis/aboard.iee.ihu.gr"
                            target="_blank"
                            >Github</a
                        >
                    </p>
                    <p class="level-item">
                        <a href="/feed" target="_blank">
                            <span class="icon has-text-warning">
                                <i class="fas fa-rss-square fa-lg"></i>
                            </span>
                        </a>
                    </p>
                </div>
            </nav>
        </div>
        <!-- Modal start -->
        <div class="modal" v-bind:class="{ 'is-active': modalOpen }">
            <div class="modal-background" @click="bodyClass(false)"></div>
            <div class="modal-content">
                <div class="box">
                    <div class="columns is-multiline is-mobile">
                        <div class="column is-full">
                            <div class="notification is-info">
                                <p class="has-text-justified">
                                    Για θέματα που αφορούν τους βαθμούς και τα
                                    μαθήματα, παρακαλώ επικοινωνήστε με τον
                                    αντίστοιχο καθηγητή και με την γραμματεία
                                    στο:
                                    <code>info [at] it.teithe.gr</code>
                                </p>
                                <p class="help is-italic">
                                    Για την έυρεση στοιχείων επικοινωνίας
                                    καθηγητή, χρησιμοποιήστε την
                                    <a
                                        href="https://apps.it.teithe.gr/search"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        >Υπηρεσία Καταλόγου</a
                                    >
                                </p>
                            </div>
                        </div>
                        <div class="column is-full">
                            <div class="notification is-primary">
                                <p class="has-text-justified">
                                    Για θέματα σχετικά με τις δηλώσεις μαθημάτων
                                    και τις υπηρεσίες που παρέχονται από το
                                    ίδρυμα (pithia, webmail, moodle κ.λ)
                                    επικοινωνήστε στο:
                                    <code>noc [at] it.teithe.gr</code>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button
                class="modal-close is-large"
                aria-label="close"
                @click="bodyClass(false)"
            ></button>
        </div>
        <!-- Modal end -->
    </footer>
</template>

<script>
export default {
    data: function() {
        return {
            modalOpen: false,
            is_admin: false,
            is_author: false
        };
    },
    mounted: function() {
        if (localStorage.getItem("user_info")) {
            this.is_author = JSON.parse(
                localStorage.getItem("user_info")
            ).is_author;
            this.is_admin = JSON.parse(
                localStorage.getItem("user_info")
            ).is_admin;
        }
    },
    methods: {
        bodyClass(val) {
            if (val == true) {
                document
                    .getElementsByTagName("html")[0]
                    .classList.add("is-clipped");
                this.modalOpen = true;
            } else if (val == false) {
                document
                    .getElementsByTagName("html")[0]
                    .classList.remove("is-clipped");
                this.modalOpen = false;
            }
        }
    }
};
</script>

<style scoped>
.footer {
    border-top: 1px solid #e1e4e8;
}
</style>
