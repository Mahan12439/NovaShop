<?php

require_once __DIR__ . '/includes/auth.php';

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {
    admin_redirect(
        'categories.php'
    );
}

admin_check_csrf();

$id =
    (int) ($_POST['id'] ?? 0);


$stmt = $pdo->prepare("
    SELECT image
    FROM categories
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$category =
    $stmt->fetch();


if ($category) {

    $delete =
        $pdo->prepare("
            DELETE FROM categories
            WHERE id = :id
        ");

    $delete->execute([
        'id' => $id
    ]);


    if (!empty($category['image'])) {

        admin_delete_image(
            'categories',
            $category['image']
        );
    }
}


admin_flash(
    'success',
    __('category_deleted')
);

admin_redirect(
    'categories.php'
);