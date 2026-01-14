<?php
$currentUrl = $_GET['url'] ?? '';
?>
<aside class="lg:w-64 flex-shrink-0">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <!-- User Info -->
        <div class="flex items-center gap-3 pb-4 border-b mb-4">
            <img src="<?= avatar(Session::user()['avatar']) ?>" alt="Avatar" class="w-12 h-12 rounded-full object-cover">
            <div>
                <p class="font-medium"><?= Session::user()['fullname'] ?></p>
                <p class="text-xs text-gray-500"><?= Session::user()['email'] ?></p>
            </div>
        </div>

        <!-- Menu -->
        <nav class="space-y-1">
            <a href="<?= BASE_URL ?>/profile" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg <?= $currentUrl === 'profile' ? 'bg-accent/10 text-accent' : 'hover:bg-gray-100' ?>">
                <i class="fas fa-user w-5"></i>
                <span>Tài khoản</span>
            </a>
            <?php if (!Session::canAccessAdmin()): ?>
            <a href="<?= BASE_URL ?>/profile/orders" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg <?= strpos($currentUrl, 'profile/order') !== false ? 'bg-accent/10 text-accent' : 'hover:bg-gray-100' ?>">
                <i class="fas fa-box w-5"></i>
                <span>Đơn hàng</span>
            </a>
            <a href="<?= BASE_URL ?>/profile/wishlist" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg <?= strpos($currentUrl, 'profile/wishlist') !== false ? 'bg-accent/10 text-accent' : 'hover:bg-gray-100' ?>">
                <i class="fas fa-heart w-5"></i>
                <span>Yêu thích</span>
            </a>
            <?php endif; ?>
            <hr class="my-2">
            <a href="<?= BASE_URL ?>/auth/logout" class="flex items-center gap-3 px-3 py-2 rounded-lg text-red-500 hover:bg-red-50">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Đăng xuất</span>
            </a>
        </nav>
    </div>
</aside>
