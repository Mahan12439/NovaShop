<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/functions.php';


// =====================================================
// Check Existing Admin
// =====================================================

$count = $pdo
    ->query("SELECT COUNT(*) FROM admins")
    ->fetchColumn();

if ((int) $count > 0) {

    die(
        'An administrator already exists. ' .
        'Delete create-admin.php.'
    );
}


$error = '';
$success = '';


// =====================================================
// Create Admin
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName =
        trim($_POST['full_name'] ?? '');

    $username =
        trim($_POST['username'] ?? '');

    $email =
        trim($_POST['email'] ?? '');

    $password =
        $_POST['password'] ?? '';

    $confirmPassword =
        $_POST['confirm_password'] ?? '';


    if (
        $fullName === '' ||
        $username === '' ||
        $email === '' ||
        $password === ''
    ) {

        $error = __('required_field');

    } elseif (
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {

        $error = __('invalid_data');

    } elseif (
        strlen($password) < 8
    ) {

        $error = __('password_minimum');

    } elseif (
        $password !== $confirmPassword
    ) {

        $error = __('password_mismatch');

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO admins
            (
                username,
                email,
                password_hash,
                full_name,
                role,
                status
            )

            VALUES
            (
                :username,
                :email,
                :password_hash,
                :full_name,
                'super_admin',
                'active'
            )
        ");

        try {

            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' =>
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    ),
                'full_name' => $fullName
            ]);

            $success =
                __('admin_created');

        } catch (PDOException $e) {

            $error =
                __('admin_create_error');
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
        <?= e(__('create_admin')) ?>
    </title>

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
                Admin Setup
            </span>
            </div>

            <div class="admin-language-switcher">

                <?php if ($currentLanguage === 'fa'): ?>

                    <a href="?lang=de">
                        🇩🇪 Deutsch
                    </a>

                <?php else: ?>

                    <a href="?lang=fa">
                        🇮🇷 فارسی
                    </a>

                <?php endif; ?>

            </div>

        </div>


        <div class="admin-login-heading">

            <h1>
                <?= __('create_admin') ?>
            </h1>

            <p>
                <?= __('create_admin_subtitle') ?>
            </p>

        </div>


        <?php if ($error !== ''): ?>

            <div class="admin-alert error">
                <?= e($error) ?>
            </div>

        <?php endif; ?>


        <?php if ($success !== ''): ?>

            <div class="admin-alert success">

                <?= e($success) ?>

                <br><br>

                <a
                    href="login.php"
                >
                    <?= __('admin_login') ?>
                </a>

            </div>

        <?php else: ?>


            <form
                method="POST"
                class="admin-login-form"
            >

                <div class="admin-form-group">

                    <label>
                        <?= __('full_name') ?>
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label>
                        <?= __('username') ?>
                    </label>

                    <input
                        type="text"
                        name="username"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label>
                        <?= __('email') ?>
                    </label>

                    <input
                        type="email"
                        name="email"
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
                        minlength="8"
                        required
                    >

                </div>


                <div class="admin-form-group">

                    <label>
                        <?= __('confirm_password') ?>
                    </label>

                    <input
                        type="password"
                        name="confirm_password"
                        minlength="8"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="admin-login-button"
                >
                    <?= __('create_admin') ?>
                </button>

            </form>


        <?php endif; ?>


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