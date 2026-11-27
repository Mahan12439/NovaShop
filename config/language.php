<?php

// =====================================================
// NovaShop - Language System
// =====================================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';


// -----------------------------------------------------
// زبان انتخاب‌شده
// -----------------------------------------------------

if (
    isset($_GET['lang']) &&
    in_array($_GET['lang'], SUPPORTED_LANGUAGES, true)
) {

    $_SESSION['language'] = $_GET['lang'];

}


// -----------------------------------------------------
// دریافت زبان فعلی
// -----------------------------------------------------

$currentLanguage =
    $_SESSION['language'] ?? DEFAULT_LANGUAGE;


// اگر زبان اشتباه بود، زبان پیش‌فرض استفاده شود
if (
    !in_array(
        $currentLanguage,
        SUPPORTED_LANGUAGES,
        true
    )
) {

    $currentLanguage = DEFAULT_LANGUAGE;

}


// -----------------------------------------------------
// اطلاعات زبان
// -----------------------------------------------------

$languageFile =
    LANGUAGE_PATH . '/' . $currentLanguage . '.php';


if (!file_exists($languageFile)) {

    die('Language file not found.');

}


$translations = require $languageFile;


// -----------------------------------------------------
// تابع ترجمه
// -----------------------------------------------------

if (!function_exists('__')) {

    function __(
        string $key,
        array $replace = []
    ): string {

        global $translations;

        $text = $translations[$key] ?? $key;

        foreach ($replace as $search => $value) {

            $text = str_replace(
                '{' . $search . '}',
                $value,
                $text
            );

        }

        return $text;
    }
}


// -----------------------------------------------------
// جهت صفحه
// -----------------------------------------------------

$pageDirection =
    ($currentLanguage === 'fa')
        ? 'rtl'
        : 'ltr';


// -----------------------------------------------------
// تگ HTML زبان
// -----------------------------------------------------

$htmlLanguage =
    ($currentLanguage === 'fa')
        ? 'fa'
        : 'de';