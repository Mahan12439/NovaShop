<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/functions.php';

if (empty($_SESSION['admin_id'])) {

    header(
        'Location: login.php'
    );

    exit;
}