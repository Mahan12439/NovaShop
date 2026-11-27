<?php

require_once __DIR__ . '/auth.php';

$pageTitle =
    $pageTitle ??
    __('dashboard');

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
        <?= e($pageTitle) ?>
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


<body class="admin-body">


<?php require __DIR__ . '/sidebar.php'; ?>


<div class="admin-wrapper">


    <header class="admin-topbar">

        <div class="admin-topbar-left">

            <button
                type="button"
                class="admin-mobile-toggle"
                id="adminMobileToggle"
            >
                ☰
            </button>


            <div>

                <span class="admin-page-label">
                    NovaShop Admin
                </span>

                <h1>
                    <?= e($pageTitle) ?>
                </h1>

            </div>

        </div>


        <div class="admin-topbar-right">

            <div class="admin-language">

                <?php if ($currentLanguage === 'fa'): ?>

                    <a
                            href="?lang=de"
                            data-language-switch
                    >
                        DE
                    </a>

                <?php else: ?>

                    <a
                            href="?lang=fa"
                            data-language-switch
                    >
                        FA
                    </a>

                <?php endif; ?>

            </div>


            <div class="admin-user">

                <div class="admin-user-avatar">
                    <?= e(
                        mb_strtoupper(
                            mb_substr(
                                $_SESSION['admin_name'] ?? 'A',
                                0,
                                1
                            )
                        )
                    ) ?>
                </div>

                <div>

                    <strong>
                        <?= e(
                            $_SESSION['admin_name'] ??
                            'Admin'
                        ) ?>
                    </strong>

                    <small>
                        <?= e(
                            $_SESSION['admin_role'] ??
                            'admin'
                        ) ?>
                    </small>

                </div>

            </div>

        </div>

    </header>


    <main class="admin-content">


<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/functions.php';

if (empty($_SESSION['admin_id'])) {

    header(
        'Location: ' .
        'login.php'
    );

    exit;
}