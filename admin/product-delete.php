<?php

require_once __DIR__ . '/includes/auth.php';

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    admin_redirect(
        'products.php'
    );
}

admin_check_csrf();

$id =
    (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    admin_redirect(
        'products.php'
    );
}


$stmt = $pdo->prepare("
    SELECT image
    FROM products
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$product =
    $stmt->fetch();


$imagesStmt =
    $pdo->prepare("
        SELECT image
        FROM product_images
        WHERE product_id = :id
    ");

$imagesStmt->execute([
    'id' => $id
]);

$images =
    $imagesStmt->fetchAll();


$delete =
    $pdo->prepare("
        DELETE FROM products
        WHERE id = :id
    ");

$delete->execute([
    'id' => $id
]);


if (
    !empty($product['image'])
) {

    admin_delete_image(
        'products',
        $product['image']
    );
}


foreach (
    $images as $image
) {

    admin_delete_image(
        'products',
        $image['image']
    );
}


admin_flash(
    'success',
    __('product_deleted')
);

admin_redirect(
    'products.php'
);