<template>
    <footer class="footer container">
        <div class="container">
            <nav class="level is-mobile">
                <div class="level-left">
                    <p class="level-item subtitle is-6">Σύστημα ανακοινώσεων</p>
                </div>

                <p class="level-item has-text-centered">
                    <a title="Επικοινωνία" 
                            href="https://www.iee.ihu.gr/support/dept_services/"
                            target="_blank">
                        <span class="icon is-medium">
                            <i class="fas fa-2x fa-envelope"></i>
                        </span>
                    </a>
                </p>

                <div class="level-right is-hidden-mobile">
                    <p class="level-item is-size-7">
                        <a href="/documentation">API Docs</a>
                    </p>
                    <p class="level-item is-size-7">
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
    </footer>
</template>

<script>
export default {
    data: function() {
        return {};
    },
};
</script>

<style scoped>
.footer {
    border-top: 1px solid #e1e4e8;
}
</style>
