<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? SITE_NAME ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a1a1a',
                        secondary: '#f5f5f5',
                        accent: '#ef4444'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Toast Notification -->
<?php if (Session::hasFlash()): $flash = Session::getFlash(); ?>
<div id="toast" class="fixed top-4 right-4 z-50 animate-slide-in">
    <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg <?= $flash['type'] === 'success' ? 'bg-green-500' : ($flash['type'] === 'error' ? 'bg-red-500' : 'bg-blue-500') ?> text-white">
        <i class="fas <?= $flash['type'] === 'success' ? 'fa-check-circle' : ($flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') ?>"></i>
        <span><?= $flash['message'] ?></span>
        <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:opacity-75">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<script>setTimeout(() => document.getElementById('toast')?.remove(), 5000);</script>
<?php endif; ?>

<!-- Header -->
<header class="bg-white shadow-sm sticky top-0 z-40">
    <!-- Top Bar -->
    <div class="bg-primary text-white text-xs py-2 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span><i class="fas fa-phone-alt mr-1"></i> Hotline: 1900 xxxx</span>
                <span><i class="fas fa-truck mr-1"></i> Miễn phí vận chuyển đơn từ 500K</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:text-accent"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-accent"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-accent"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Header - All in one row -->
    <?php $currentUrl = $_GET['url'] ?? ''; ?>
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn" class="lg:hidden text-2xl" aria-label="Mở menu">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>

            <!-- Logo -->
            <a href="<?= BASE_URL ?>" class="flex-shrink-0">
                <h1 class="text-xl md:text-2xl font-bold text-primary">
                    <i class="fas fa-shoe-prints text-accent"></i> SNEAKER
                </h1>
            </a>

            <!-- Menu (Left after logo) -->
            <nav class="hidden lg:flex flex-1">
                <ul class="flex items-center gap-6 text-sm font-medium">
                    <li><a href="<?= BASE_URL ?>" class="hover:text-accent transition <?= empty($currentUrl) ? 'text-accent font-bold' : '' ?>">TRANG CHỦ</a></li>
                    <li><a href="<?= BASE_URL ?>/home/products?gender=Male" class="hover:text-accent transition <?= strpos($currentUrl, 'products') !== false && ($_GET['gender'] ?? '') === 'Male' ? 'text-accent font-bold' : '' ?>">NAM</a></li>
                    <li><a href="<?= BASE_URL ?>/home/products?gender=Female" class="hover:text-accent transition <?= strpos($currentUrl, 'products') !== false && ($_GET['gender'] ?? '') === 'Female' ? 'text-accent font-bold' : '' ?>">NỮ</a></li>
                    <li><a href="<?= BASE_URL ?>/home/products?sort=sale" class="hover:text-accent transition <?= strpos($currentUrl, 'products') !== false && ($_GET['sort'] ?? '') === 'sale' ? 'text-accent font-bold' : '' ?>">SALE</a></li>
                    <li class="relative group">
                        <a href="<?= BASE_URL ?>/home/products" class="hover:text-accent transition flex items-center gap-1">
                            THƯƠNG HIỆU <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <div class="absolute left-0 top-full mt-3 w-48 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                            <?php 
                            $brands = $brands ?? [];
                            foreach ($brands as $brand): ?>
                            <a href="<?= BASE_URL ?>/home/products?brand=<?= $brand['id'] ?>" 
                               class="block px-4 py-2.5 hover:bg-gray-50 text-sm"><?= $brand['name'] ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Search Bar (Right side) -->
            <form action="<?= BASE_URL ?>/home/products" method="GET" class="hidden lg:block relative w-56" id="searchForm">
                <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." 
                       id="searchInput"
                       autocomplete="off"
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-full text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-accent/20"
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                       aria-label="Tìm kiếm sản phẩm">
                <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-accent" aria-label="Tìm kiếm">
                    <i class="fas fa-search text-sm" aria-hidden="true"></i>
                </button>
                <!-- Search Suggestions -->
                <div id="searchSuggestions" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-lg shadow-lg border hidden z-50 max-h-80 overflow-y-auto">
                </div>
            </form>

            <!-- Right Icons -->
            <div class="flex items-center gap-4">
                <!-- Search Mobile -->
                <button id="searchMobileBtn" class="lg:hidden text-xl" aria-label="Tìm kiếm">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </button>

                <!-- Wishlist -->
                <?php if (Session::isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>/profile/wishlist" class="hidden sm:block text-xl hover:text-accent" aria-label="Danh sách yêu thích">
                    <i class="far fa-heart" aria-hidden="true"></i>
                </a>
                <?php endif; ?>

                <!-- Cart -->
                <a href="<?= BASE_URL ?>/cart" class="text-xl hover:text-accent relative" aria-label="Giỏ hàng">
                    <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                    <span id="cartCount" class="absolute -top-2 -right-2 bg-accent text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" aria-label="Số sản phẩm trong giỏ">
                        <?= count(Session::get('cart', [])) ?>
                    </span>
                </a>

                <!-- User -->
                <?php if (Session::isLoggedIn()): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 hover:text-accent">
                        <img src="<?= avatar(Session::user()['avatar']) ?>" alt="Avatar" 
                             class="w-8 h-8 rounded-full object-cover border">
                        <span class="hidden lg:inline text-sm"><?= Session::user()['fullname'] ?></span>
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <?php if (Session::canAccessAdmin()): ?>
                        <a href="<?= BASE_URL ?>/admin" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm">
                            <i class="fas fa-tachometer-alt w-5"></i> Quản trị
                        </a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/profile" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm">
                            <i class="fas fa-user w-5"></i> Tài khoản
                        </a>
                        <a href="<?= BASE_URL ?>/profile/orders" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm">
                            <i class="fas fa-box w-5"></i> Đơn hàng
                        </a>
                        <a href="<?= BASE_URL ?>/profile/purchaseHistory" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm">
                            <i class="fas fa-history w-5"></i> Lịch sử mua hàng
                        </a>
                        <a href="<?= BASE_URL ?>/profile/wishlist" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm">
                            <i class="fas fa-heart w-5"></i> Yêu thích
                        </a>
                        <hr class="my-1">
                        <a href="<?= BASE_URL ?>/auth/logout" class="flex items-center gap-2 px-4 py-2.5 hover:bg-gray-50 text-sm text-red-500">
                            <i class="fas fa-sign-out-alt w-5"></i> Đăng xuất
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <a href="<?= BASE_URL ?>/auth/login" class="flex items-center gap-2 hover:text-accent">
                    <i class="far fa-user text-xl"></i>
                    <span class="hidden lg:inline text-sm">Đăng nhập</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div id="mobileMenu" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeMobileMenu()"></div>
    <div class="absolute left-0 top-0 bottom-0 w-72 bg-white transform -translate-x-full transition-transform" id="mobileMenuContent">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-bold text-lg">Menu</h2>
            <button onclick="closeMobileMenu()" class="text-2xl">&times;</button>
        </div>
        <nav class="p-4">
            <a href="<?= BASE_URL ?>" class="block py-3 border-b">Trang chủ</a>
            <a href="<?= BASE_URL ?>/home/products?gender=Male" class="block py-3 border-b">Nam</a>
            <a href="<?= BASE_URL ?>/home/products?gender=Female" class="block py-3 border-b">Nữ</a>
            <a href="<?= BASE_URL ?>/home/products?sort=sale" class="block py-3 border-b text-accent">Sale</a>
            <a href="<?= BASE_URL ?>/home/products" class="block py-3 border-b">Tất cả sản phẩm</a>
        </nav>
    </div>
</div>

<!-- Mobile Search -->
<div id="mobileSearch" class="fixed inset-x-0 top-0 bg-white p-4 shadow-lg z-50 hidden">
    <form action="<?= BASE_URL ?>/home/products" method="GET" class="flex gap-2">
        <input type="text" name="q" placeholder="Tìm kiếm..." class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg">
            <i class="fas fa-search"></i>
        </button>
        <button type="button" onclick="closeMobileSearch()" class="px-4 py-2 border rounded-lg">
            <i class="fas fa-times"></i>
        </button>
    </form>
</div>

<main class="flex-1">
