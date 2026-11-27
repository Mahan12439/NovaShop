<?php

require_once __DIR__ . '/includes/auth.php';

$pageTitle = __('products');

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';


// =====================================================
// Build Query
// =====================================================

$sql = "
    SELECT
        p.id,
        p.sku,
        p.price,
        p.compare_price,
        p.stock,
        p.image,
        p.status,
        p.featured,
        pt.name,
        ct.name AS category_name

    FROM products p

    INNER JOIN product_translations pt
        ON pt.product_id = p.id
        AND pt.language = :product_language

    LEFT JOIN category_translations ct
        ON ct.category_id = p.category_id
        AND ct.language = :category_language

    WHERE 1 = 1
";

$params = [
    'product_language' => $currentLanguage,
    'category_language' => $currentLanguage
];


// =====================================================
// Search — use distinct placeholders
// =====================================================

if ($q !== '') {
    $sql .= "
        AND (
            pt.name LIKE :name_query
            OR p.sku LIKE :sku_query
        )
    ";

    $searchValue = '%' . $q . '%';

    $params['name_query'] = $searchValue;
    $params['sku_query'] = $searchValue;
}


// =====================================================
// Status Filter
// =====================================================

if (
    in_array(
        $status,
        admin_product_statuses(),
        true
    )
) {
    $sql .= " AND p.status = :product_status ";

    $params['product_status'] = $status;
}

$sql .= " ORDER BY p.id DESC LIMIT 100 ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll();


// Load page only after processing the query
require_once __DIR__ . '/includes/header.php';

?>


    <div class="admin-page-toolbar">

        <form method="GET" class="admin-search-form">

            <input
                    type="search"
                    name="q"
                    value="<?= e($q) ?>"
                    placeholder="<?= e(__('search')) ?>"
                    aria-label="<?= e(__('search')) ?>"
            >

            <select name="status" aria-label="<?= e(__('status')) ?>">

                <option value="">
                    <?= e(__('all_statuses')) ?>
                </option>

                <?php foreach (admin_product_statuses() as $item): ?>

                    <option
                            value="<?= e($item) ?>"
                        <?= $status === $item ? 'selected' : '' ?>
                    >
                        <?= e(__($item)) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <button type="submit" class="admin-button">
                <?= e(__('search')) ?>
            </button>

            <?php if ($q !== '' || $status !== ''): ?>
                <a
                        href="products.php"
                        class="admin-button"
                >
                    <?= e(__('clear')) ?>
                </a>
            <?php endif; ?>

        </form>

        <a
                href="product-add.php"
                class="admin-button primary"
        >
            + <?= e(__('add_product')) ?>
        </a>

    </div>


    <div class="admin-panel">

        <div class="admin-panel-heading">

            <div>
                <span>NovaShop</span>
                <h2><?= e(__('product_management')) ?></h2>
            </div>

            <span class="admin-result-count">
            <?= count($products) ?>
        </span>

        </div>


        <div class="admin-table-wrapper admin-products-table-wrapper">

            <table class="admin-table admin-products-table">

                <thead>
                <tr>
                    <th><?= e(__('product')) ?></th>
                    <th><?= e(__('sku')) ?></th>
                    <th><?= e(__('category')) ?></th>
                    <th><?= e(__('price')) ?></th>
                    <th><?= e(__('stock')) ?></th>
                    <th><?= e(__('status')) ?></th>
                    <th><?= e(__('actions')) ?></th>
                </tr>
                </thead>

                <tbody>

                <?php if (!empty($products)): ?>

                    <?php foreach ($products as $product): ?>

                        <tr>

                            <td>
                                <div class="admin-product-cell">

                                    <?php
                                    $image = !empty($product['image'])
                                        ? '../storage/products/' . $product['image']
                                        : '../assets/images/placeholders/product.jpg';
                                    ?>

                                    <img
                                            src="<?= e($image) ?>"
                                            alt="<?= e($product['name']) ?>"
                                            loading="lazy"
                                    >

                                    <strong title="<?= e($product['name']) ?>">
                                        <?= e($product['name']) ?>
                                    </strong>

                                </div>
                            </td>

                            <td><?= e($product['sku']) ?></td>

                            <td>
                                <?= e($product['category_name'] ?? '-') ?>
                            </td>

                            <td><?= formatPrice($product['price']) ?></td>

                            <td>
                                <?= (int)$product['stock'] > 0
                                    ? e(__('in_stock'))
                                    : e(__('out_of_stock')) ?>
                            </td>

                            <td>
                            <span class="admin-status status-<?= e($product['status']) ?>">
                                <?= e(__($product['status'])) ?>
                            </span>
                            </td>

                            <td>
                                <div class="admin-actions">

                                    <a
                                            href="product-edit.php?id=<?= (int)$product['id'] ?>"
                                            class="admin-table-action"
                                    >
                                        <?= e(__('edit')) ?>
                                    </a>

                                    <form
                                            method="POST"
                                            action="product-delete.php"
                                            onsubmit="return confirm('<?= e(__('confirm_delete')) ?>')"
                                    >
                                        <?= admin_csrf_field() ?>

                                        <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$product['id'] ?>"
                                        >

                                        <button
                                                type="submit"
                                                class="admin-table-action danger"
                                        >
                                            <?= e(__('delete')) ?>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="admin-empty-table">
                            <?= e(__('no_products')) ?>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>