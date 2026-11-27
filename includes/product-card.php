<?php

$productImage = !empty($product['image'])
    ? 'storage/products/' . $product['image']
    : 'assets/images/placeholders/product.jpg';

$hasDiscount =
    !empty($product['compare_price']) &&
    $product['compare_price'] > $product['price'];

$discountPercent = 0;

if ($hasDiscount) {

    $discountPercent = round(
        (
            ($product['compare_price'] - $product['price'])
            / $product['compare_price']
        ) * 100
    );
}


$averageRating =
    isset($product['average_rating'])
        ? (float) $product['average_rating']
        : 5.0;

$reviewCount =
    isset($product['review_count'])
        ? (int) $product['review_count']
        : 0;

$fullStars = floor($averageRating);

$hasHalfStar =
    ($averageRating - $fullStars) >= 0.5;

?>

<article class="product-card">

    <a
            href="product.php?slug=<?= e($product['slug']) ?>"
            class="product-image-wrapper"
    >

        <?php if ($hasDiscount): ?>

            <span class="product-discount">
                -<?= $discountPercent ?>%
            </span>

        <?php endif; ?>


        <?php if (!empty($product['featured'])): ?>

            <span class="product-featured">
                ★
            </span>

        <?php endif; ?>


        <img
                src="<?= e($productImage) ?>"
                alt="<?= e($product['name']) ?>"
                class="product-image"
                loading="lazy"
        >

    </a>


    <div class="product-content">


        <!-- Rating -->

        <div class="product-rating">

            <div class="rating-stars">

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


            <small>
                <?= number_format($averageRating, 1) ?>
                (<?= $reviewCount ?>)
            </small>

        </div>


        <!-- Name -->

        <a
                href="product.php?slug=<?= e($product['slug']) ?>"
                class="product-name"
        >
            <?= e($product['name']) ?>
        </a>


        <!-- Description -->

        <?php if (!empty($product['short_description'])): ?>

            <p class="product-short-description">
                <?= e($product['short_description']) ?>
            </p>

        <?php endif; ?>


        <!-- Bottom -->

        <div class="product-bottom">

            <div class="product-prices">

                <strong class="product-price">
                    <?= formatPrice($product['price']) ?>
                </strong>


                <?php if ($hasDiscount): ?>

                    <del class="product-old-price">
                        <?= formatPrice($product['compare_price']) ?>
                    </del>

                <?php endif; ?>

            </div>


            <button
                    type="button"
                    class="add-cart-button"
                    data-product-id="<?= (int) $product['id'] ?>"
                    aria-label="<?= e(__('add_to_cart')) ?>"
            >
                +
            </button>

        </div>

    </div>

</article>