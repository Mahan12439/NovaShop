</main>


<footer class="site-footer">

    <div class="container">

        <div class="footer-grid">


            <!-- About -->
            <div class="footer-column">

                <div class="footer-logo">
                    <span class="logo-mark">N</span>

                    <span class="logo-text">
                        NovaShop
                    </span>
                </div>

                <p class="footer-description">
                    <?= __('site_description') ?>
                </p>

            </div>


            <!-- Links -->
            <div class="footer-column">

                <h3 class="footer-title">
                    <?= __('products') ?>
                </h3>

                <a href="products.php">
                    <?= __('products') ?>
                </a>

                <a href="products.php">
                    <?= __('categories') ?>
                </a>

                <a href="favorites.php">
                    <?= __('favorites') ?>
                </a>

            </div>


            <!-- Account -->
            <div class="footer-column">

                <h3 class="footer-title">
                    <?= __('profile') ?>
                </h3>

                <a href="login.php">
                    <?= __('login') ?>
                </a>

                <a href="register.php">
                    <?= __('register') ?>
                </a>

                <a href="orders.php">
                    <?= __('orders') ?>
                </a>

            </div>


            <!-- Support -->
            <div class="footer-column">

                <h3 class="footer-title">
                    <?= __('about_us') ?>
                </h3>

                <a href="#">
                    <?= __('contact_us') ?>
                </a>

                <a href="#">
                    <?= __('privacy') ?>
                </a>

                <a href="#">
                    <?= __('terms') ?>
                </a>

            </div>

        </div>


        <div class="footer-bottom">

            <p>
                © <?= date('Y') ?>
                <?= SITE_NAME ?>
                -
                <?= __('all_rights_reserved') ?>
                |
                <?= __('created_by') ?>
            </p>

            <div class="footer-language">

                <?php if ($currentLanguage === 'fa'): ?>

                    <a href="?lang=de">
                        🇩🇪 Deutsch
                    </a>

                <?php else: ?>

                    <a href="?lang=fa">
                        🇮🇷 فارسی
                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</footer>


<script
    src="assets/js/app.js"
></script>

</body>
</html><?php
