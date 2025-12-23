<?php
/**
 * Front Controller - Entry Point
 * Tất cả request đều đi qua file này
 */

// Load config
require_once __DIR__ . '/../config/config.php';

// Start session
require_once BASE_PATH . '/app/core/Session.php';
Session::start();

// Load core files
require_once BASE_PATH . '/app/core/Database.php';
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/core/Middleware.php';
require_once BASE_PATH . '/app/helpers/functions.php';

// Load và chạy App Router
require_once BASE_PATH . '/app/core/App.php';
$app = new App();
