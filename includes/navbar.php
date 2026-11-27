<?php
?>

    <header class="site-header">

    <div class="container">

        <nav class="navbar">

            <!-- Logo -->
            <a
                href="index.php"
                class="navbar-logo"
            >
                <span class="logo-mark">N</span>

                <span class="logo-text">
                    NovaShop
                </span>
            </a>


            <!-- Desktop Navigation -->
            <div class="navbar-menu">

                <a
                    href="index.php"
                    class="nav-link"
                >
                    <?= __('home') ?>
                </a>

                <a
                    href="products.php"
                    class="nav-link"
                >
                    <?= __('products') ?>
                </a>

                <a
                    href="products.php"
                    class="nav-link"
                >
                    <?= __('categories') ?>
                </a>

                <a
                    href="favorites.php"
                    class="nav-link"
                >
                    <?= __('favorites') ?>
                </a>

            </div>


            <!-- Navbar Actions -->
            <div class="navbar-actions">

                <!-- Search -->
                <a
                    href="search.php"
                    class="nav-icon"
                    aria-label="<?= __('search') ?>"
                    title="<?= __('search') ?>"
                >
                    🔍
                </a>


                <!-- Favorites -->
                <a
                    href="favorites.php"
                    class="nav-icon"
                    aria-label="<?= __('favorites') ?>"
                    title="<?= __('favorites') ?>"
                >
                    ♡
                </a>


                <!-- Cart -->
                <a
                    href="cart.php"
                    class="nav-icon cart-icon"
                    aria-label="<?= __('cart') ?>"
                    title="<?= __('cart') ?>"
                >
                    🛒
                    <span class="cart-count">0</span>
                </a>


                <!-- Account -->
                <a
                    href="profile.php"
                    class="nav-icon"
                    aria-label="<?= __('profile') ?>"
                    title="<?= __('profile') ?>"
                >
                    👤
                </a>


                <!-- Language -->
                <div class="language-switcher">

                    <?php if ($currentLanguage === 'fa'): ?>

                        <a
                            href="?lang=de"
                            class="language-button"
                            title="Deutsch"
                        >
                            DE
                        </a>

                    <?php else: ?>

                        <a
                            href="?lang=fa"
                            class="language-button"
                            title="فارسی"
                        >
                            FA
                        </a>

                    <?php endif; ?>

                </div>


                <!-- Mobile Menu -->
                <button
                    type="button"
                    class="mobile-menu-button"
                    id="mobileMenuButton"
                    aria-label="Menu"
                >
                    ☰
                </button>

            </div>

        </nav>


        <!-- Mobile Navigation -->
        <div
            class="mobile-menu"
            id="mobileMenu"
        >

            <a
                href="index.php"
                class="mobile-nav-link"
            >
                <?= __('home') ?>
            </a>

            <a
                href="products.php"
                class="mobile-nav-link"
            >
                <?= __('products') ?>
            </a>

            <a
                href="products.php"
                class="mobile-nav-link"
            >
                <?= __('categories') ?>
            </a>

            <a
                href="favorites.php"
                class="mobile-nav-link"
            >
                <?= __('favorites') ?>
            </a>

            <a
                href="cart.php"
                class="mobile-nav-link"
            >
                <?= __('cart') ?>
            </a>

            <a
                href="profile.php"
                class="mobile-nav-link"
            >
                <?= __('profile') ?>
            </a>

        </div>

    </div>

    </header><?php
