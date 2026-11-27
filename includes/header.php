<?php

require_once __DIR__ . '/../config/language.php';

$pageTitle = $pageTitle ?? SITE_NAME;
?>

    <!DOCTYPE html>
<html
    lang="<?= htmlspecialchars($htmlLanguage) ?>"
    dir="<?= htmlspecialchars($pageDirection) ?>"
>
    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <meta
            name="description"
            content="<?= htmlspecialchars($pageDescription ?? SITE_NAME) ?>"
        >

        <title>
            <?= htmlspecialchars($pageTitle) ?>
        </title>

        <!-- Google Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">

        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link
            href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet"
        >

        <!-- Main CSS -->
        <link
            rel="stylesheet"
            href="assets/css/style.css"
        >

        <!-- Responsive CSS -->
        <link
            rel="stylesheet"
            href="assets/css/responsive.css"
        >

        <?php if ($currentLanguage === 'fa'): ?>

            <link
                rel="stylesheet"
                href="assets/css/rtl.css"
            >

        <?php endif; ?>

    </head>

<body>

<?php require_once INCLUDE_PATH . '/navbar.php'; ?>

    <main class="site-main"><?php
