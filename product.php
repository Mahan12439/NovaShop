<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';


// =====================================================
// Product Slug
// =====================================================

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {

    header(
        'Location: products.php'
    );

    exit;
}


// =====================================================
// Get Product
// =====================================================

$product = getProductBySlug(
    $pdo,
    $currentLanguage,
    $slug
);

if (!$product) {

    http_response_code(404);

    require_once __DIR__ . '/404.php';

    exit;
}


// =====================================================
// Product Data
// =====================================================

$images = getProductImages(
    $pdo,
    (int) $product['id'],
    $product['image']
);

$rating = getProductRating(
    $pdo,
    (int) $product['id']
);

$reviews = getProductReviews(
    $pdo,
    (int) $product['id']
);

$relatedProducts = [];

if (!empty($product['category_id'])) {

    $relatedProducts = getRelatedProducts(
        $pdo,
        $currentLanguage,
        (int) $product['category_id'],
        (int) $product['id'],
        4
    );
}


// =====================================================
// Page Meta
// =====================================================

$pageTitle = !empty($product['meta_title'])
    ? $product['meta_title']
    : $product['name'];

$pageDescription = !empty($product['meta_description'])
    ? $product['meta_description']
    : ($product['short_description'] ?? '');


// =====================================================
// Header
// =====================================================

require_once __DIR__ . '/includes/header.php';

?>


<section class="product-page">

    <div class="container">


        <!-- =================================================
             Breadcrumb
             ================================================= -->

        <div class="product-breadcrumb">

            <a href="index.php">
                <?= __('home') ?>
            </a>

            <span>/</span>

            <?php if (!empty($product['category_name'])): ?>

                <span>
                    <?= e($product['category_name']) ?>
                </span>

                <span>/</span>

            <?php endif; ?>

            <span>
                <?= e($product['name']) ?>
            </span>

        </div>


        <!-- =================================================
             Product Heading
             ================================================= -->

        <div class="product-page-heading">

            <span class="section-label">
                <?= __('product') ?>
            </span>

            <h1>
                <?= e($product['name']) ?>
            </h1>

        </div>


        <!-- =================================================
             Product Main
             ================================================= -->

        <div class="product-main">


            <!-- =================================================
                 Gallery
                 ================================================= -->

            <div class="product-gallery">

                <?php

                $firstImage = $images[0] ?? null;

                $firstImageUrl = $firstImage
                    ? 'storage/products/' . $firstImage
                    : 'assets/images/placeholders/product.jpg';

                ?>


                <div class="product-main-image-wrapper">

                    <img
                            src="<?= e($firstImageUrl) ?>"
                            alt="<?= e($product['name']) ?>"
                            class="product-main-image"
                            id="productMainImage"
                    >

                </div>


                <?php if (count($images) > 1): ?>

                    <div class="product-thumbnails">

                        <?php foreach ($images as $index => $image): ?>

                            <button
                                    type="button"
                                    class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                    data-image="<?= e('storage/products/' . $image) ?>"
                            >

                                <img
                                        src="<?= e('storage/products/' . $image) ?>"
                                        alt="<?= e($product['name']) ?>"
                                >

                            </button>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 Product Information
                 ================================================= -->

            <div class="product-info">


                <!-- Rating -->

                <div class="product-rating-large">

                    <div class="rating-stars">

                        <?php

                        $averageRating =
                            (float) $rating['average_rating'];

                        $fullStars =
                            floor($averageRating);

                        $hasHalfStar =
                            ($averageRating - $fullStars) >= 0.5;

                        ?>

                        <?php for ($i = 1; $i <= 5; $i++): ?>

                            <?php if ($i <= $fullStars): ?>

                                <span class="active">
                                    ★
                                </span>

                            <?php elseif (
                                $i === $fullStars + 1 &&
                                $hasHalfStar
                            ): ?>

                                <span class="half">
                                    ★
                                </span>

                            <?php else: ?>

                                <span>
                                    ★
                                </span>

                            <?php endif; ?>

                        <?php endfor; ?>

                    </div>


                    <span>
                        <?= number_format(
                            $averageRating,
                            1
                        ) ?>
                    </span>


                    <a href="#reviews">

                        (
                        <?= $rating['review_count'] ?>
                        <?= __('reviews') ?>
                        )

                    </a>

                </div>


                <!-- Price -->

                <div class="product-price-box">

                    <strong>
                        <?= formatPrice($product['price']) ?>
                    </strong>

                    <?php if (
                        !empty($product['compare_price']) &&
                        $product['compare_price'] > $product['price']
                    ): ?>

                        <del>
                            <?= formatPrice(
                                $product['compare_price']
                            ) ?>
                        </del>

                    <?php endif; ?>

                </div>


                <!-- Short Description -->

                <?php if (!empty($product['short_description'])): ?>

                    <p class="product-main-description">
                        <?= e(
                            $product['short_description']
                        ) ?>
                    </p>

                <?php endif; ?>


                <!-- Meta -->

                <div class="product-meta">

                    <div>

                        <span>
                            <?= __('sku') ?>
                        </span>

                        <strong>
                            <?= e($product['sku']) ?>
                        </strong>

                    </div>


                    <div>

                        <span>
                            <?= __('stock') ?>
                        </span>

                        <strong>

                            <?php if (
                                (int) $product['stock'] > 0
                            ): ?>

                                <span class="stock-success">
                                    <?= __('in_stock') ?>
                                </span>

                            <?php else: ?>

                                <span class="stock-danger">
                                    <?= __('out_of_stock') ?>
                                </span>

                            <?php endif; ?>

                        </strong>

                    </div>

                </div>


                <!-- Cart -->

                <?php if ((int) $product['stock'] > 0): ?>

                    <div class="product-actions">

                        <div class="quantity-selector">

                            <button
                                    type="button"
                                    id="quantityMinus"
                            >
                                −
                            </button>


                            <input
                                    type="number"
                                    id="productQuantity"
                                    value="1"
                                    min="1"
                                    max="<?= (int) $product['stock'] ?>"
                            >


                            <button
                                    type="button"
                                    id="quantityPlus"
                            >
                                +
                            </button>

                        </div>


                        <button
                                type="button"
                                class="product-add-button"
                                data-product-id="<?= (int) $product['id'] ?>"
                        >
                            <?= __('add_to_cart') ?>
                        </button>

                    </div>

                <?php else: ?>

                    <button
                            type="button"
                            class="product-add-button disabled"
                            disabled
                    >
                        <?= __('out_of_stock') ?>
                    </button>

                <?php endif; ?>

            </div>

        </div>


        <!-- =================================================
             Specifications
             ================================================= -->

        <section class="product-section">

            <div class="product-section-heading">

                <span class="section-label">
                    NovaShop
                </span>

                <h2>
                    <?= __('specifications') ?>
                </h2>

            </div>


            <div class="specifications-box">

                <div class="specification-row">

                    <span>
                        <?= __('sku') ?>
                    </span>

                    <strong>
                        <?= e($product['sku']) ?>
                    </strong>

                </div>


                <div class="specification-row">

                    <span>
                        <?= __('category') ?>
                    </span>

                    <strong>
                        <?= e(
                            $product['category_name'] ?? '-'
                        ) ?>
                    </strong>

                </div>

            </div>

        </section>


        <!-- =================================================
             Description
             ================================================= -->

        <section class="product-section">

            <div class="product-section-heading">

                <span class="section-label">
                    NovaShop
                </span>

                <h2>
                    <?= __('description') ?>
                </h2>

            </div>


            <div class="product-description-box">

                <?= nl2br(
                    e($product['description'] ?? '')
                ) ?>

            </div>

        </section>


        <!-- =================================================
             Reviews
             ================================================= -->

        <section
                class="product-section"
                id="reviews"
        >

            <div class="product-section-heading">

                <span class="section-label">
                    NovaShop
                </span>

                <h2>
                    <?= __('reviews') ?>
                </h2>

            </div>


            <div class="reviews-layout">


                <!-- Rating Summary -->

                <div class="review-summary">

                    <div class="review-score">

                        <?= number_format(
                            $averageRating,
                            1
                        ) ?>

                    </div>


                    <div class="rating-stars large">

                        <?php for (
                            $i = 1;
                            $i <= 5;
                            $i++
                        ): ?>

                            <?php if ($i <= $fullStars): ?>

                                <span class="active">
                                    ★
                                </span>

                            <?php elseif (
                                $i === $fullStars + 1 &&
                                $hasHalfStar
                            ): ?>

                                <span class="half">
                                    ★
                                </span>

                            <?php else: ?>

                                <span>
                                    ★
                                </span>

                            <?php endif; ?>

                        <?php endfor; ?>

                    </div>


                    <p>

                        <?= $rating['review_count'] ?>

                        <?= __('reviews') ?>

                    </p>

                </div>


                <!-- Reviews List -->

                <div class="reviews-list">

                    <?php if (!empty($reviews)): ?>

                        <?php foreach (
                            $reviews as $review
                        ): ?>

                            <article class="review-card">

                                <div class="review-header">

                                    <strong>

                                        <?= e(
                                            trim(
                                                $review['first_name']
                                                . ' ' .
                                                $review['last_name']
                                            )
                                        ) ?>

                                    </strong>


                                    <span>

                                        <?= date(
                                            'Y/m/d',
                                            strtotime(
                                                $review['created_at']
                                            )
                                        ) ?>

                                    </span>

                                </div>


                                <div class="rating-stars">

                                    <?php for (
                                        $i = 1;
                                        $i <= 5;
                                        $i++
                                    ): ?>

                                        <?php if (
                                            $i <=
                                            (int) $review['rating']
                                        ): ?>

                                            <span class="active">
                                                ★
                                            </span>

                                        <?php else: ?>

                                            <span>
                                                ★
                                            </span>

                                        <?php endif; ?>

                                    <?php endfor; ?>

                                </div>


                                <p class="review-comment">

                                    <?= nl2br(
                                        e(
                                            $review['comment']
                                        )
                                    ) ?>

                                </p>

                            </article>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty-reviews">

                            <span>
                                ★
                            </span>

                            <p>
                                <?= __('no_reviews') ?>
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- =================================================
                 Write Review
                 ================================================= -->

            <div class="write-review">

                <div class="write-review-heading">

                    <h3>
                        <?= __('write_review') ?>
                    </h3>

                </div>


                <?php if (!empty($_SESSION['user_id'])): ?>

                    <form
                            id="reviewForm"
                            class="review-form"
                            data-translations='<?= json_encode([
                                "review_submitted" =>
                                    __("review_submitted"),

                                "already_reviewed" =>
                                    __("already_reviewed"),

                                "comment_required" =>
                                    __("comment_required"),

                                "comment_too_short" =>
                                    __("comment_too_short"),

                                "comment_too_long" =>
                                    __("comment_too_long"),

                                "invalid_rating" =>
                                    __("invalid_rating"),

                                "invalid_product" =>
                                    __("invalid_product"),

                                "invalid_request" =>
                                    __("invalid_request"),

                                "login_required" =>
                                    __("login_required"),

                                "error" =>
                                    __("error")
                            ],
                                JSON_UNESCAPED_UNICODE |
                                JSON_HEX_APOS |
                                JSON_HEX_QUOT) ?>'
                    >

                        <input
                                type="hidden"
                                name="product_id"
                                value="<?= (int) $product['id'] ?>"
                        >


                        <!-- Rating -->

                        <div class="review-field">

                            <label>
                                <?= __('your_rating') ?>
                            </label>


                            <div
                                    class="review-rating-input"
                                    dir="ltr"
                            >

                                <input
                                        type="radio"
                                        name="rating"
                                        id="rating5"
                                        value="5"
                                        checked
                                >

                                <label for="rating5">
                                    ★
                                </label>


                                <input
                                        type="radio"
                                        name="rating"
                                        id="rating4"
                                        value="4"
                                >

                                <label for="rating4">
                                    ★
                                </label>


                                <input
                                        type="radio"
                                        name="rating"
                                        id="rating3"
                                        value="3"
                                >

                                <label for="rating3">
                                    ★
                                </label>


                                <input
                                        type="radio"
                                        name="rating"
                                        id="rating2"
                                        value="2"
                                >

                                <label for="rating2">
                                    ★
                                </label>


                                <input
                                        type="radio"
                                        name="rating"
                                        id="rating1"
                                        value="1"
                                >

                                <label for="rating1">
                                    ★
                                </label>

                            </div>

                        </div>


                        <!-- Comment -->

                        <div class="review-field">

                            <label for="reviewComment">
                                <?= __('your_comment') ?>
                            </label>


                            <textarea
                                    id="reviewComment"
                                    name="comment"
                                    rows="5"
                                    maxlength="2000"
                                    required
                                    placeholder="<?= e(
                                        __('review_placeholder')
                                    ) ?>"
                            ></textarea>

                        </div>


                        <button
                                type="submit"
                                class="review-submit"
                        >
                            <?= __('submit_review') ?>
                        </button>


                        <div
                                class="review-form-message"
                                id="reviewFormMessage"
                        ></div>

                    </form>

                <?php else: ?>

                    <div class="review-login-message">

                        <p>
                            <?= __('login_to_review') ?>
                        </p>


                        <a
                                href="login.php"
                                class="hero-button"
                        >
                            <?= __('login') ?>
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </section>


        <!-- =================================================
             Related Products
             ================================================= -->

        <?php if (!empty($relatedProducts)): ?>

            <section class="product-section">

                <div class="section-heading">

                    <div>

                        <span class="section-label">
                            NovaShop
                        </span>

                        <h2 class="section-title">
                            <?= __('related_products') ?>
                        </h2>

                    </div>

                </div>


                <div class="products-grid">

                    <?php foreach (
                        $relatedProducts as $relatedProduct
                    ): ?>

                        <?php

                        $product =
                            $relatedProduct;

                        require INCLUDE_PATH .
                            '/product-card.php';

                        ?>

                    <?php endforeach; ?>

                </div>

            </section>

        <?php endif; ?>


    </div>

</section>


<script src="assets/js/product.js"></script>


<?php

require_once __DIR__ . '/includes/footer.php';

?>
