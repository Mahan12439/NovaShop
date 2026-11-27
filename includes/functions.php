<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/language.php';


// =====================================================
// Escape HTML
// =====================================================

function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


// =====================================================
// Format Price
// =====================================================

function formatPrice($price): string
{
    global $currentLanguage;

    if ($currentLanguage === 'de') {

        return number_format(
                (float) $price,
                2,
                ',',
                '.'
            ) . ' €';

    }

    return number_format(
            (float) $price,
            2,
            '.',
            ','
        ) . ' €';
}


// =====================================================
// Get Categories
// =====================================================

function getCategories(
    PDO $pdo,
    string $language,
    int $limit = 8
): array {

    $limit = max(1, min($limit, 50));

    $sql = "
        SELECT
            c.id,
            c.image,
            c.sort_order,
            ct.name,
            ct.slug
        FROM categories c

        INNER JOIN category_translations ct
            ON ct.category_id = c.id
            AND ct.language = :language

        WHERE c.status = 'active'

        ORDER BY
            c.sort_order ASC,
            c.id DESC

        LIMIT $limit
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'language' => $language
    ]);

    return $stmt->fetchAll();
}


// =====================================================
// Get Products
// =====================================================

// =====================================================
// Get Products
// =====================================================

function getProducts(
    PDO $pdo,
    string $language,
    int $limit = 8,
    bool $featured = false
): array {

    $limit = max(1, min($limit, 50));

    $featuredCondition = '';

    if ($featured) {

        $featuredCondition = "
            AND p.featured = 1
        ";

    }

    $sql = "
        SELECT

            p.id,
            p.sku,
            p.price,
            p.compare_price,
            p.stock,
            p.image,
            p.featured,

            pt.name,
            pt.slug,
            pt.short_description,

            (
                SELECT COALESCE(
                    AVG(r.rating),
                    5
                )
                FROM reviews r
                WHERE
                    r.product_id = p.id
                    AND r.status = 'approved'
            ) AS average_rating,

            (
                SELECT COUNT(*)
                FROM reviews r2
                WHERE
                    r2.product_id = p.id
                    AND r2.status = 'approved'
            ) AS review_count

        FROM products p

        INNER JOIN product_translations pt
            ON pt.product_id = p.id
            AND pt.language = :language

        WHERE
            p.status = 'active'

            $featuredCondition

        ORDER BY
            p.created_at DESC

        LIMIT $limit
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'language' => $language
    ]);

    return $stmt->fetchAll();
}

// =====================================================
// Get Sliders
// =====================================================

function getSliders(
    PDO $pdo,
    string $language
): array {

    $sql = "
        SELECT
            s.id,
            s.image,
            s.mobile_image,
            s.button_url,

            st.title,
            st.subtitle,
            st.button_text

        FROM sliders s

        INNER JOIN slider_translations st
            ON st.slider_id = s.id
            AND st.language = :language

        WHERE
            s.status = 'active'

        ORDER BY
            s.sort_order ASC,
            s.id DESC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'language' => $language
    ]);

    return $stmt->fetchAll();
}


// =====================================================
// Get Single Product By Slug
// =====================================================

function getProductBySlug(
    PDO $pdo,
    string $language,
    string $slug
): ?array {

    $sql = "
        SELECT
            p.id,
            p.category_id,
            p.sku,
            p.price,
            p.compare_price,
            p.stock,
            p.image,
            p.featured,

            pt.name,
            pt.slug,
            pt.short_description,
            pt.description,
            pt.meta_title,
            pt.meta_description,

            ct.name AS category_name

        FROM products p

        INNER JOIN product_translations pt
            ON pt.product_id = p.id
            AND pt.language = :product_language

        LEFT JOIN category_translations ct
            ON ct.category_id = p.category_id
            AND ct.language = :category_language

        WHERE
            p.status = 'active'
            AND pt.slug = :slug

        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'product_language' => $language,
        'category_language' => $language,
        'slug' => $slug
    ]);

    $product = $stmt->fetch();

    return $product ?: null;
}

// =====================================================
// Get Product Images
// =====================================================

function getProductImages(
    PDO $pdo,
    int $productId,
    ?string $mainImage = null
): array {

    $images = [];

    if (!empty($mainImage)) {
        $images[] = $mainImage;
    }

    $stmt = $pdo->prepare("
        SELECT image
        FROM product_images
        WHERE product_id = :product_id
        ORDER BY
            sort_order ASC,
            id ASC
    ");

    $stmt->execute([
        'product_id' => $productId
    ]);

    $galleryImages = $stmt->fetchAll();

    foreach ($galleryImages as $image) {

        if (
            !empty($image['image']) &&
            !in_array($image['image'], $images, true)
        ) {

            $images[] = $image['image'];
        }
    }

    return $images;
}

// =====================================================
// Get Product Rating
// =====================================================

function getProductRating(
    PDO $pdo,
    int $productId
): array {

    $stmt = $pdo->prepare("
        SELECT
            AVG(rating) AS average_rating,
            COUNT(*) AS review_count
        FROM reviews
        WHERE
            product_id = :product_id
            AND status = 'approved'
    ");

    $stmt->execute([
        'product_id' => $productId
    ]);

    $result = $stmt->fetch();

    $reviewCount =
        (int) ($result['review_count'] ?? 0);

    $averageRating =
        $reviewCount > 0
            ? round((float) $result['average_rating'], 1)
            : 5.0;

    return [
        'average_rating' => $averageRating,
        'review_count' => $reviewCount
    ];
}


// =====================================================
// Get Product Reviews
// =====================================================

function getProductReviews(
    PDO $pdo,
    int $productId
): array {

    $stmt = $pdo->prepare("
        SELECT
            r.id,
            r.rating,
            r.comment,
            r.created_at,

            u.first_name,
            u.last_name

        FROM reviews r

        INNER JOIN users u
            ON u.id = r.user_id

        WHERE
            r.product_id = :product_id
            AND r.status = 'approved'

        ORDER BY
            r.created_at DESC
    ");

    $stmt->execute([
        'product_id' => $productId
    ]);

    return $stmt->fetchAll();
}


// =====================================================
// Related Products
// =====================================================

function getRelatedProducts(
    PDO $pdo,
    string $language,
    int $categoryId,
    int $excludeProductId,
    int $limit = 4
): array {

    $limit = max(1, min($limit, 20));

    $sql = "
        SELECT

            p.id,
            p.price,
            p.compare_price,
            p.stock,
            p.image,
            p.featured,

            pt.name,
            pt.slug,
            pt.short_description,

            (
                SELECT COALESCE(
                    AVG(r.rating),
                    5
                )
                FROM reviews r
                WHERE
                    r.product_id = p.id
                    AND r.status = 'approved'
            ) AS average_rating,

            (
                SELECT COUNT(*)
                FROM reviews r2
                WHERE
                    r2.product_id = p.id
                    AND r2.status = 'approved'
            ) AS review_count

        FROM products p

        INNER JOIN product_translations pt
            ON pt.product_id = p.id
            AND pt.language = :language

        WHERE
            p.category_id = :category_id
            AND p.id != :exclude_id
            AND p.status = 'active'

        ORDER BY
            p.created_at DESC

        LIMIT $limit
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'language' => $language,
        'category_id' => $categoryId,
        'exclude_id' => $excludeProductId
    ]);

    return $stmt->fetchAll();
}
