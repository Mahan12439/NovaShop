<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';

// اگر قبلاً وارد شده
if (!empty($_SESSION['admin_id'])) {

    header(
        'Location: index.php'
    );

    exit;
}


// =====================================================
// CSRF
// =====================================================

if (empty($_SESSION['admin_csrf'])) {

    $_SESSION['admin_csrf'] =
        bin2hex(random_bytes(32));
}


$error = '';


// =====================================================
// Login
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf =
        $_POST['csrf_token'] ?? '';

    $username =
        trim($_POST['username'] ?? '');

    $password =
        $_POST['password'] ?? '';


    if (
        !hash_equals(
            $_SESSION['admin_csrf'],
            $csrf
        )
    ) {

        $error = __('invalid_request');

    } elseif (
        $username === '' ||
        $password === ''
    ) {

        $error = __('admin_login_required');

    } else {

        $stmt = $pdo->prepare("
            SELECT
                id,
                username,
                email,
                password_hash,
                full_name,
                role,
                status
            FROM admins
            WHERE
                (username = :username
                OR email = :email)
                AND status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            'username' => $username,
            'email' => $username
        ]);

        $admin = $stmt->fetch();


        if (
            $admin &&
            password_verify(
                $password,
                $admin['password_hash']
            )
        ) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] =
                (int) $admin['id'];

            $_SESSION['admin_username'] =
                $admin['username'];

            $_SESSION['admin_name'] =
                $admin['full_name'];

            $_SESSION['admin_role'] =
                $admin['role'];


            $update = $pdo->prepare("
                UPDATE admins
                SET last_login_at = NOW()
                WHERE id = :id
            ");

            $update->execute([
                'id' => $admin['id']
            ]);


            header(
                'Location: ' .
                'index.php'
            );

            exit;

        } else {

            $error = __('admin_login_error');
        }
    }
}

?>

<!DOCTYPE html>
<html
    lang="<?= e($htmlLanguage) ?>"
    dir="<?= e($pageDirection) ?>"
>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e(__('admin_login')) ?>
        -
        <?= e(SITE_NAME) ?>
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>

<body class="admin-login-body">


<div class="admin-login-page">

    <div class="admin-login-card">

        <div class="admin-login-logo">

            <span class="admin-logo-mark">
                N
            </span>

            <div>

                <strong>
                    NovaShop
                </strong>

                <span>
                    Admin Panel
                </span>

            </div>

            <div class="admin-language admin-login-language"
                 aria-label="Language selection">

                <a href="?lang=fa"
                   lang="fa"
                   class="<?= $pageDirection === 'rtl' ? 'active' : '' ?>">
                    FA
                </a>

                <a href="?lang=de"
                   lang="de"
                   class="<?= $pageDirection === 'ltr' ? 'active' : '' ?>">
                    DE
                </a>

            </div>

        </div>


        <div class="admin-login-heading">

            <h1>
                <?= __('admin_login') ?>
            </h1>

            <p>
                <?= __('admin_login_subtitle') ?>
            </p>

        </div>


        <?php if ($error !== ''): ?>

            <div class="admin-alert error">

                <?= e($error) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="admin-login-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['admin_csrf']) ?>"
            >


            <div class="admin-form-group">

                <label>
                    <?= __('username_or_email') ?>
                </label>

                <input
                    type="text"
                    name="username"
                    autocomplete="username"
                    required
                >

            </div>


            <div class="admin-form-group">

                <label>
                    <?= __('password') ?>
                </label>

                <input
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="admin-login-button"
            >
                <?= __('login') ?>
            </button>

        </form>


        <a
            href="../index.php"
            class="back-to-site"
        >
            ←
            <?= __('back_to_store') ?>
        </a>

    </div>

</div>


</body>
</html>