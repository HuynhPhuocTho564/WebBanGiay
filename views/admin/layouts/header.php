<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? (Session::isAdmin() ? 'Admin Panel' : 'Staff Panel') ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e293b',
                        secondary: '#334155',
                        accent: '#3b82f6'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-primary text-white transform -translate-x-full lg:translate-x-0 transition-transform">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-white/10">
            <a href="<?= BASE_URL ?>/admin" class="text-xl font-bold">
                <i class="fas fa-shoe-prints text-accent"></i> <?= Session::isAdmin() ? 'ADMIN' : 'STAFF' ?>
            </a>
        </div>

        <!-- User Info -->
        <div class="p-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <img src="<?= avatar(Session::user()['avatar']) ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <p class="font-medium text-sm"><?= Session::user()['fullname'] ?></p>
                    <p class="text-xs text-gray-400"><?= roleName(Session::role()) ?></p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-1">
            <a href="<?= BASE_URL ?>/admin" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('admin') && !isset($_GET['url']) ? 'bg-white/10' : '' ?>">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a>

            <p class="text-xs text-gray-500 uppercase tracking-wider pt-4 pb-2 px-4">Quản lý bán hàng</p>
            
            <a href="<?= BASE_URL ?>/adminorder" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('adminorder') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-shopping-cart w-5"></i>
                <span>Đơn hàng</span>
                <?php 
                $pendingCount = Database::getInstance()->count("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
                if ($pendingCount > 0): 
                ?>
                <span class="ml-auto bg-red-500 text-xs px-2 py-0.5 rounded-full"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= BASE_URL ?>/adminproduct" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('adminproduct') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-box w-5"></i>
                <span>Sản phẩm</span>
            </a>

            <a href="<?= BASE_URL ?>/admincategory" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('admincategory') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-tags w-5"></i>
                <span>Danh mục</span>
            </a>

            <a href="<?= BASE_URL ?>/adminbrand" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('adminbrand') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-copyright w-5"></i>
                <span>Thương hiệu</span>
            </a>

            <?php if (Session::isAdmin()): ?>
            <p class="text-xs text-gray-500 uppercase tracking-wider pt-4 pb-2 px-4">Quản trị hệ thống</p>
            
            <a href="<?= BASE_URL ?>/adminuser" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('adminuser') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-users w-5"></i>
                <span>Người dùng</span>
            </a>

            <a href="<?= BASE_URL ?>/admincoupon" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('admincoupon') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-ticket-alt w-5"></i>
                <span>Mã giảm giá</span>
            </a>

            <a href="<?= BASE_URL ?>/adminreport" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition <?= isActive('adminreport') ? 'bg-white/10' : '' ?>">
                <i class="fas fa-chart-bar w-5"></i>
                <span>Báo cáo</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Back to Site -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
            <a href="<?= BASE_URL ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition text-gray-400">
                <i class="fas fa-external-link-alt w-5"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-6">
            <!-- Mobile Menu Toggle -->
            <button id="sidebarToggle" class="lg:hidden text-xl">
                <i class="fas fa-bars"></i>
            </button>

            <div class="hidden lg:block">
                <h1 class="text-lg font-semibold"><?= $pageTitle ?? 'Dashboard' ?></h1>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <?php 
                $pendingOrders = Database::getInstance()->fetchAll(
                    "SELECT id, fullname, total_money, order_date FROM orders WHERE status = 'pending' ORDER BY order_date DESC LIMIT 5"
                );
                $notifCount = count($pendingOrders);
                ?>
                <div class="relative group">
                    <button class="relative text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($notifCount > 0): ?>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?= $notifCount ?></span>
                        <?php endif; ?>
                    </button>
                    <!-- Notification Dropdown -->
                    <div class="absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <div class="px-4 py-3 border-b font-medium flex justify-between items-center">
                            <span>Thông báo</span>
                            <?php if ($notifCount > 0): ?>
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full"><?= $notifCount ?> mới</span>
                            <?php endif; ?>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <?php if (empty($pendingOrders)): ?>
                            <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                <i class="fas fa-check-circle text-2xl mb-2"></i>
                                <p>Không có thông báo mới</p>
                            </div>
                            <?php else: ?>
                            <?php foreach ($pendingOrders as $order): ?>
                            <a href="<?= BASE_URL ?>/adminorder/detail/<?= $order['id'] ?>" 
                               class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 border-b last:border-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shopping-cart text-yellow-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">Đơn hàng mới #<?= $order['id'] ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($order['fullname']) ?> - <?= number_format($order['total_money'], 0, ',', '.') ?>đ</p>
                                    <p class="text-xs text-gray-400 mt-1"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                                </div>
                            </a>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($notifCount > 0): ?>
                        <a href="<?= BASE_URL ?>/adminorder?status=pending" class="block px-4 py-2 text-center text-sm text-accent hover:bg-gray-50 border-t">
                            Xem tất cả đơn hàng chờ xử lý
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative group">
                    <button class="flex items-center gap-2">
                        <img src="<?= avatar(Session::user()['avatar']) ?>" alt="Avatar" class="w-8 h-8 rounded-full object-cover">
                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                    </button>
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <a href="<?= BASE_URL ?>/profile" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm">
                            <i class="fas fa-user w-5"></i> Tài khoản
                        </a>
                        <hr>
                        <a href="<?= BASE_URL ?>/auth/logout" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm text-red-500">
                            <i class="fas fa-sign-out-alt w-5"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Toast Notification -->
        <?php if (Session::hasFlash()): $flash = Session::getFlash(); ?>
        <div id="toast" class="fixed top-20 right-4 z-50 animate-slide-in">
            <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg <?= $flash['type'] === 'success' ? 'bg-green-500' : ($flash['type'] === 'error' ? 'bg-red-500' : 'bg-blue-500') ?> text-white">
                <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <span><?= $flash['message'] ?></span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2">&times;</button>
            </div>
        </div>
        <script>setTimeout(() => document.getElementById('toast')?.remove(), 5000);</script>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="flex-1 p-4 lg:p-6">
