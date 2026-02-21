<?php
// Site base URL – আপনার installation অনুযায়ী ঠিক করুন
define('BASE_URL', 'http://localhost/personal_blog/');
define('SITE_NAME', 'My Personal Blog');

// Upload directory – Windows-এ সঠিক পাথ
define('UPLOAD_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', BASE_URL . 'assets/uploads/');

// Error reporting (development environment)
error_reporting(E_ALL);
ini_set('display_errors', 1);