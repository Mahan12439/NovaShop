<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = SITE_NAME;
$pageDescription = __('site_description');

$sliders = getSliders(
    $pdo,
    $currentLanguage
);

$categories = getCategories(
    $pdo,
    $currentLanguage,
    8
);

$featuredProducts = getProducts(
    $pdo,
    $currentLanguage,
    8,
    true
);

$newProducts = getProducts(
    $pdo,
    $currentLanguage,
    8,
    false
);

require_once __DIR__ . '/includes/header.php';

?>


    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="hero-section">

        <div class="container">

            <?php if (!empty($sliders)): ?>

                <div class="hero-slider" id="heroSlider">

                    <?php foreach ($sliders as $index => $slider): ?>

                        <div
                                class="hero-slide <?= $index === 0 ? 'active' : '' ?>"
                        >

                            <div class="hero-content">

                                <?php if (!empty($slider['title'])): ?>

                                    <span class="hero-label">
                                    NovaShop
                                </span>

                                    <h1 class="hero-title">
                                        <?= e($slider['title']) ?>
                                    </h1>

                                <?php endif; ?>


                                <?php if (!empty($slider['subtitle'])): ?>

                                    <p class="hero-description">
                                        <?= e($slider['subtitle']) ?>
                                    </p>

                                <?php endif; ?>


                                <?php if (!empty($slider['button_text'])): ?>

                                    <a
                                            href="<?= e($slider['button_url'] ?: '#') ?>"
                                            class="hero-button"
                                    >
                                        <?= e($slider['button_text']) ?>

                                        <span>→</span>
                                    </a>

                                <?php endif; ?>

                            </div>


                            <div class="hero-image-wrapper">

                                <img
                                        src="storage/sliders/<?= e($slider['image']) ?>"
                                        alt="<?= e($slider['title'] ?: SITE_NAME) ?>"
                                        class="hero-image"
                                >

                            </div>

                        </div>

                    <?php endforeach; ?>


                    <?php if (count($sliders) > 1): ?>

                        <div class="hero-controls">

                            <button
                                    type="button"
                                    class="hero-arrow"
                                    id="heroPrev"
                            >
                                ←
                            </button>

                            <div
                                    class="hero-dots"
                                    id="heroDots"
                            >

                                <?php foreach ($sliders as $index => $slider): ?>

                                    <button
                                            type="button"
                                            class="hero-dot <?= $index === 0 ? 'active' : '' ?>"
                                            data-slide="<?= $index ?>"
                                    ></button>

                                <?php endforeach; ?>

                            </div>

                            <button
                                    type="button"
                                    class="hero-arrow"
                                    id="heroNext"
                            >
                                →
                            </button>

                        </div>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="hero-fallback">

                    <div class="hero-content">

                    <span class="hero-label">
                        NovaShop
                    </span>

                        <h1 class="hero-title">
                            <?= __('welcome_to_novashop') ?>
                        </h1>

                        <p class="hero-description">
                            <?= __('hero_description') ?>
                        </p>

                        <a
                                href="products.php"
                                class="hero-button"
                        >
                            <?= __('view_products') ?>
                            <span>→</span>
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </section>



    <!-- =====================================================
         CATEGORIES
         ===================================================== -->

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div>
                <span class="section-label">
                    NovaShop
                </span>

                    <h2 class="section-title">
                        <?= __('categories') ?>
                    </h2>
                </div>

                <a
                        href="products.php"
                        class="section-link"
                >
                    <?= __('view_all') ?>
                    →
                </a>

            </div>


            <div class="categories-grid">

                <?php foreach ($categories as $category): ?>

                    <?php

                    $categoryImage = !empty($category['image'])
                        ? 'storage/categories/' . $category['image']
                        : 'assets/images/placeholders/product.jpg';

                    ?>

                    <a
                            href="products.php?category=<?= e($category['slug']) ?>"
                            class="category-card"
                    >

                        <div class="category-image-wrapper">

                            <img
                                    src="<?= e($categoryImage) ?>"
                                    alt="<?= e($category['name']) ?>"
                                    class="category-image"
                                    loading="lazy"
                            >

                        </div>

                        <span class="category-name">
                        <?= e($category['name']) ?>
                    </span>

                    </a>

                <?php endforeach; ?>

            </div>

        </div>

    </section>



    <!-- =====================================================
         FEATURED PRODUCTS
         ===================================================== -->

<?php if (!empty($featuredProducts)): ?>

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div>

                <span class="section-label">
                    NovaShop
                </span>

                    <h2 class="section-title">
                        <?= __('featured_products') ?>
                    </h2>

                </div>

                <a
                        href="products.php"
                        class="section-link"
                >
                    <?= __('view_all') ?>
                    →
                </a>

            </div>


            <div class="products-grid">

                <?php foreach ($featuredProducts as $product): ?>

                    <?php
                    require INCLUDE_PATH . '/product-card.php';
                    ?>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

<?php endif; ?>



    <!-- =====================================================
         BENEFITS
         ===================================================== -->

    <section class="benefits-section">

        <div class="container">

            <div class="benefits-grid">

                <div class="benefit-card">

                    <div class="benefit-icon">
                        🚚
                    </div>

                    <div>

                        <h3>
                            <?= __('fast_shipping') ?>
                        </h3>

                        <p>
                            <?= __('fast_shipping_desc') ?>
                        </p>

                    </div>

                </div>


                <div class="benefit-card">

                    <div class="benefit-icon">
                        🔒
                    </div>

                    <div>

                        <h3>
                            <?= __('secure_payment') ?>
                        </h3>

                        <p>
                            <?= __('secure_payment_desc') ?>
                        </p>

                    </div>

                </div>


                <div class="benefit-card">

                    <div class="benefit-icon">
                        ↩
                    </div>

                    <div>

                        <h3>
                            <?= __('easy_return') ?>
                        </h3>

                        <p>
                            <?= __('easy_return_desc') ?>
                        </p>

                    </div>

                </div>


                <div class="benefit-card">

                    <div class="benefit-icon">
                        ♡
                    </div>

                    <div>

                        <h3>
                            <?= __('support') ?>
                        </h3>

                        <p>
                            <?= __('support_desc') ?>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- =====================================================
         NEW PRODUCTS
         ===================================================== -->

<?php if (!empty($newProducts)): ?>

    <section class="section">

        <div class="container">

            <div class="section-heading">

                <div>

                <span class="section-label">
                    NovaShop
                </span>

                    <h2 class="section-title">
                        <?= __('new_products') ?>
                    </h2>

                </div>

                <a
                        href="products.php"
                        class="section-link"
                >
                    <?= __('view_all') ?>
                    →
                </a>

            </div>


            <div class="products-grid">

                <?php foreach ($newProducts as $product): ?>

                    <?php
                    require INCLUDE_PATH . '/product-card.php';
                    ?>

                <?php endforeach; ?>

            </div>

        </div>

    </section>

<?php endif; ?>



    <!-- =====================================================
         CTA
         ===================================================== -->

    <section class="section">

        <div class="container">

            <div class="home-cta">

                <div>

                <span class="section-label">
                    NovaShop
                </span>

                    <h2>
                        <?= __('discover_collection') ?>
                    </h2>

                    <p>
                        <?= __('discover_collection_desc') ?>
                    </p>

                </div>

                <a
                        href="products.php"
                        class="hero-button"
                >
                    <?= __('view_products') ?>
                    →
                </a>

            </div>

        </div>

    </section>


<?php

require_once __DIR__ . '/includes/footer.php';

?>