<?php

// =====================================================
// NovaShop - Main Configuration
// =====================================================

// نام سایت
define('SITE_NAME', 'NovaShop');

// زبان پیش‌فرض
define('DEFAULT_LANGUAGE', 'fa');

// زبان‌های فعال
define('SUPPORTED_LANGUAGES', [
    'fa',
    'de'
]);

// URLهای HTML به‌صورت نسبی و متناسب با محل هر صفحه نوشته می‌شوند.
// مسیرهای فایل‌سیستم پایین همچنان مطلق و مستقل از URL هستند.

// -----------------------------------------------------
// مسیر اصلی پروژه
// -----------------------------------------------------
define('BASE_PATH', dirname(__DIR__));

// -----------------------------------------------------
// مسیرهای مهم
// -----------------------------------------------------
define('CONFIG_PATH', BASE_PATH . '/config');
define('LANGUAGE_PATH', BASE_PATH . '/languages');
define('INCLUDE_PATH', BASE_PATH . '/includes');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('API_PATH', BASE_PATH . '/api');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('STORAGE_PATH', BASE_PATH . '/storage');

// -----------------------------------------------------
// اطلاعات فروشگاه
// -----------------------------------------------------
define('CURRENCY', '€');
define('CURRENCY_CODE', 'EUR');

// -----------------------------------------------------
// حالت توسعه
// -----------------------------------------------------
define('APP_ENV', 'development');

// در حالت توسعه خطاها نمایش داده شوند
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');

    error_reporting(0);
}