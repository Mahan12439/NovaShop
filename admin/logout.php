<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session.php';

unset(
    $_SESSION['admin_id'],
    $_SESSION['admin_username'],
    $_SESSION['admin_name'],
    $_SESSION['admin_role']
);

session_regenerate_id(true);

header(
    'Location: login.php'
);

exit;