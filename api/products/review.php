<?php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';


// =====================================================
// JSON Response
// =====================================================

function reviewResponse(
    bool $success,
    string $messageKey
): void {

    echo json_encode(
        [
            'success' => $success,
            'message_key' => $messageKey
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

// =====================================================
// Authentication
// =====================================================

if (empty($_SESSION['user_id'])) {

    reviewResponse(
        false,
        'login_required'
    );

}


// =====================================================
// Method
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    reviewResponse(
        false,
        'invalid_request'
    );

}


// =====================================================
// Input
// =====================================================

$userId = (int) $_SESSION['user_id'];

$productId =
    (int) ($_POST['product_id'] ?? 0);

$rating =
    (int) ($_POST['rating'] ?? 0);

$comment =
    trim($_POST['comment'] ?? '');


// =====================================================
// Validation
// =====================================================

if ($productId <= 0) {

    reviewResponse(
        false,
        'invalid_product'
    );

}


if ($rating < 1 || $rating > 5) {

    reviewResponse(
        false,
        'invalid_rating'
    );

}


if ($comment === '') {

    reviewResponse(
        false,
        'comment_required'
    );

}


if (mb_strlen($comment) < 5) {

    reviewResponse(
        false,
        'comment_too_short'
    );

}


if (mb_strlen($comment) > 2000) {

    reviewResponse(
        false,
        'comment_too_long'
    );

}


// =====================================================
// Check Product
// =====================================================

$stmt = $pdo->prepare("
    SELECT id
    FROM products
    WHERE
        id = :product_id
        AND status = 'active'
    LIMIT 1
");

$stmt->execute([
    'product_id' => $productId
]);

if (!$stmt->fetch()) {

    reviewResponse(
        false,
        'invalid_product'
    );

}


// =====================================================
// Prevent Duplicate Review
// =====================================================

$stmt = $pdo->prepare("
    SELECT id
    FROM reviews
    WHERE
        user_id = :user_id
        AND product_id = :product_id
    LIMIT 1
");

$stmt->execute([
    'user_id' => $userId,
    'product_id' => $productId
]);

if ($stmt->fetch()) {

    reviewResponse(
        false,
        'already_reviewed'
    );

}


// =====================================================
// Insert Review
// =====================================================

$stmt = $pdo->prepare("
    INSERT INTO reviews
    (
        user_id,
        product_id,
        rating,
        comment,
        status
    )

    VALUES
    (
        :user_id,
        :product_id,
        :rating,
        :comment,
        'pending'
    )
");

$stmt->execute([
    'user_id' => $userId,
    'product_id' => $productId,
    'rating' => $rating,
    'comment' => $comment
]);


reviewResponse(
    true,
    'review_submitted'
);