<?php

require_once __DIR__ . '/../../includes/functions.php';


// =====================================================
// Admin Redirect
// =====================================================

function admin_redirect(string $path): void
{
    header('Location: ' . ltrim($path, '/'));
    exit;
}


// =====================================================
// CSRF
// =====================================================

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf_token'])) {

        $_SESSION['admin_csrf_token'] =
            bin2hex(random_bytes(32));
    }

    return $_SESSION['admin_csrf_token'];
}


function admin_csrf_field(): string
{
    return '
        <input
            type="hidden"
            name="csrf_token"
            value="' . e(admin_csrf_token()) . '"
        >
    ';
}


function admin_check_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['admin_csrf_token']) ||
        !hash_equals(
            $_SESSION['admin_csrf_token'],
            $token
        )
    ) {

        http_response_code(403);

        die('Invalid security token.');
    }
}


// =====================================================
// Flash
// =====================================================

function admin_flash(
    string $type,
    string $message
): void {

    $_SESSION['admin_flash'] = [
        'type' => $type,
        'message' => $message
    ];
}


function admin_get_flash(): ?array
{
    $flash =
        $_SESSION['admin_flash'] ?? null;

    unset($_SESSION['admin_flash']);

    return $flash;
}


// =====================================================
// Image Upload
// =====================================================

function admin_upload_image(
    array $file,
    string $folder
): ?string {

    if (
        !isset($file['error']) ||
        $file['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return null;
    }

    if (
        $file['error'] !== UPLOAD_ERR_OK
    ) {
        throw new RuntimeException(
            'Upload failed.'
        );
    }

    if (
        !isset($file['tmp_name']) ||
        !is_uploaded_file($file['tmp_name'])
    ) {
        throw new RuntimeException(
            'Invalid uploaded file.'
        );
    }

    $maxSize = 5 * 1024 * 1024;

    if ($file['size'] > $maxSize) {
        throw new RuntimeException(
            'Image is too large.'
        );
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $finfo->file(
        $file['tmp_name']
    );

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException(
            'Invalid image format.'
        );
    }

    if (
        @getimagesize(
            $file['tmp_name']
        ) === false
    ) {
        throw new RuntimeException(
            'Invalid image.'
        );
    }

    $extension =
        $allowed[$mime];

    $filename =
        bin2hex(
            random_bytes(12)
        ) . '.' . $extension;

    $directory =
        STORAGE_PATH . '/' . trim(
            $folder,
            '/'
        );

    if (!is_dir($directory)) {

        if (!mkdir(
            $directory,
            0755,
            true
        )) {
            throw new RuntimeException(
                'Could not create upload folder.'
            );
        }
    }

    $destination =
        $directory . '/' . $filename;

    if (!move_uploaded_file(
        $file['tmp_name'],
        $destination
    )) {

        throw new RuntimeException(
            'Could not save image.'
        );
    }

    return $filename;
}


// =====================================================
// Delete Storage File
// =====================================================

function admin_delete_image(
    string $folder,
    ?string $filename
): void {

    if (empty($filename)) {
        return;
    }

    $filename = basename($filename);

    $path =
        STORAGE_PATH .
        '/' .
        trim($folder, '/') .
        '/' .
        $filename;

    if (
        is_file($path)
    ) {
        @unlink($path);
    }
}


// =====================================================
// Status Helpers
// =====================================================

function admin_product_statuses(): array
{
    return [
        'active',
        'inactive',
        'draft'
    ];
}


function admin_order_statuses(): array
{
    return [
        'pending',
        'confirmed',
        'processing',
        'shipped',
        'delivered',
        'cancelled'
    ];
}


function admin_payment_statuses(): array
{
    return [
        'pending',
        'paid',
        'failed',
        'refunded'
    ];
}