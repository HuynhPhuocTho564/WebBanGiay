<?php
/**
 * Cấu hình chung cho ứng dụng Sneaker Store
 */

// Ngăn truy cập trực tiếp
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// ===========================================
// CẤU HÌNH DATABASE
// ===========================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_giay_db');
define('DB_USER', 'root');
define('DB_PASS', 'tKg@!1478cbA');
define('DB_CHARSET', 'utf8mb4');

// ===========================================
// CẤU HÌNH URL
// ===========================================
define('BASE_URL', 'http://localhost/shop_giay_project/public');
define('ASSETS_URL', BASE_URL . '/assets');

// ===========================================
// CẤU HÌNH UPLOAD
// ===========================================
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');
define('AVATAR_PATH', UPLOAD_PATH . '/avatars');
define('PRODUCT_PATH', UPLOAD_PATH . '/products');

// ===========================================
// CẤU HÌNH KHÁC
// ===========================================
define('SITE_NAME', 'Sneaker Store');
define('ITEMS_PER_PAGE', 12);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (Tắt khi deploy production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
