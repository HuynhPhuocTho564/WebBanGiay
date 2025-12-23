<!-- Hero Banner Slider -->
<section class="relative">
    <div class="bg-gradient-to-r from-gray-900 to-gray-700 text-white">
        <div class="container mx-auto px-4 py-12 md:py-20">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="flex-1 text-center md:text-left">
                    <span class="inline-block px-3 py-1 bg-accent rounded-full text-sm mb-4">New Arrival</span>
                    <h2 class="text-3xl md:text-5xl font-bold mb-4">Bộ Sưu Tập<br>Mùa Đông 2024</h2>
                    <p class="text-gray-300 mb-6 max-w-md">Khám phá những đôi giày sneaker hot nhất với ưu đãi lên đến 50%</p>
                    <a href="<?= BASE_URL ?>/home/products" class="inline-block px-8 py-3 bg-accent hover:bg-red-600 rounded-full font-medium transition">
                        Mua ngay <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="flex-1">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600" 
                         alt="Banner" class="w-full max-w-md mx-auto drop-shadow-2xl">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?= BASE_URL ?>/home/products?gender=Male" class="group relative overflow-hidden rounded-xl aspect-square">
            <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400" 
                 alt="Nam" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-black/40 flex items-end p-4">
                <span class="text-white font-bold text-lg">GIÀY NAM</span>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/home/products?gender=Female" class="group relative overflow-hidden rounded-xl aspect-square">
            <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400" 
                 alt="Nữ" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-black/40 flex items-end p-4">
                <span class="text-white font-bold text-lg">GIÀY NỮ</span>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/home/products?sort=sale" class="group relative overflow-hidden rounded-xl aspect-square">
            <img src="https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=400" 
                 alt="Sale" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-accent/60 flex items-end p-4">
                <span class="text-white font-bold text-lg">SALE UP TO 50%</span>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/home/products" class="group relative overflow-hidden rounded-xl aspect-square">
            <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400" 
                 alt="New" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-black/40 flex items-end p-4">
                <span class="text-white font-bold text-lg">HÀNG MỚI VỀ</span>
            </div>
        </a>
    </div>
</section>

<!-- Flash Sale -->
<?php if (!empty($saleProducts)): ?>
<section class="bg-accent/5 py-10">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <h2 class="text-xl md:text-2xl font-bold text-accent">
                    <i class="fas fa-bolt"></i> FLASH SALE
                </h2>
                <!-- Countdown Timer -->
                <div class="hidden md:flex items-center gap-1 text-sm font-mono">
                    <span id="countdown-hours" class="bg-primary text-white px-2 py-1 rounded min-w-[36px] text-center">00</span>
                    <span class="text-primary font-bold">:</span>
                    <span id="countdown-minutes" class="bg-primary text-white px-2 py-1 rounded min-w-[36px] text-center">00</span>
                    <span class="text-primary font-bold">:</span>
                    <span id="countdown-seconds" class="bg-primary text-white px-2 py-1 rounded min-w-[36px] text-center">00</span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/home/products?sort=sale" class="text-accent hover:underline text-sm">
                Xem tất cả <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach (array_slice($saleProducts, 0, 4) as $product): ?>
            <?php include BASE_PATH . '/views/components/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
// Flash Sale Countdown - Đồng bộ với thời gian thực
function initFlashSaleCountdown() {
    const hoursEl = document.getElementById('countdown-hours');
    const minutesEl = document.getElementById('countdown-minutes');
    const secondsEl = document.getElementById('countdown-seconds');
    
    if (!hoursEl) return;

    function getNextSaleEnd() {
        const now = new Date();
        // Flash sale kết thúc vào 23:59:59 mỗi ngày
        const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
        
        // Nếu đã qua thời gian, chuyển sang ngày mai
        if (now >= endOfDay) {
            endOfDay.setDate(endOfDay.getDate() + 1);
        }
        return endOfDay;
    }

    let saleEndTime = getNextSaleEnd();

    function updateCountdown() {
        const now = new Date();
        let diff = saleEndTime - now;

        // Nếu hết thời gian, reset về ngày mới
        if (diff <= 0) {
            saleEndTime = getNextSaleEnd();
            diff = saleEndTime - now;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        hoursEl.textContent = String(hours).padStart(2, '0');
        minutesEl.textContent = String(minutes).padStart(2, '0');
        secondsEl.textContent = String(seconds).padStart(2, '0');
    }

    // Cập nhật ngay lập tức
    updateCountdown();
    
    // Cập nhật mỗi giây
    setInterval(updateCountdown, 1000);
}

// Khởi chạy khi DOM ready
document.addEventListener('DOMContentLoaded', initFlashSaleCountdown);
</script>
<?php endif; ?>

<!-- New Arrivals -->
<section class="container mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl md:text-2xl font-bold">SẢN PHẨM MỚI</h2>
        <a href="<?= BASE_URL ?>/home/products?sort=newest" class="text-accent hover:underline text-sm">
            Xem tất cả <i class="fas fa-chevron-right"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <?php foreach ($newProducts as $product): ?>
        <?php include BASE_PATH . '/views/components/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- Banner CTA -->
<section class="container mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-primary to-gray-800 rounded-2xl overflow-hidden">
        <div class="flex flex-col md:flex-row items-center">
            <div class="flex-1 p-8 md:p-12 text-white text-center md:text-left">
                <h3 class="text-2xl md:text-3xl font-bold mb-3">Ưu đãi thành viên</h3>
                <p class="text-gray-300 mb-6">Đăng ký ngay để nhận voucher giảm 100K cho đơn hàng đầu tiên</p>
                <a href="<?= BASE_URL ?>/auth/register" class="inline-block px-6 py-3 bg-accent hover:bg-red-600 rounded-full font-medium transition">
                    Đăng ký ngay
                </a>
            </div>
            <div class="flex-1 p-4">
                <img src="https://images.unsplash.com/photo-1556906781-9a412961c28c?w=500" 
                     alt="Promo" class="w-full max-w-sm mx-auto rounded-xl">
            </div>
        </div>
    </div>
</section>

<!-- Best Sellers -->
<section class="container mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl md:text-2xl font-bold">BÁN CHẠY NHẤT</h2>
        <a href="<?= BASE_URL ?>/home/products?sort=popular" class="text-accent hover:underline text-sm">
            Xem tất cả <i class="fas fa-chevron-right"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <?php foreach ($bestSellers as $product): ?>
        <?php include BASE_PATH . '/views/components/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- Brands -->
<section class="bg-gray-100 py-10">
    <div class="container mx-auto px-4">
        <h2 class="text-xl md:text-2xl font-bold text-center mb-8">THƯƠNG HIỆU NỔI BẬT</h2>
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-16">
            <?php foreach ($brands as $brand): ?>
            <a href="<?= BASE_URL ?>/home/products?brand=<?= $brand['id'] ?>" 
               class="grayscale hover:grayscale-0 transition opacity-60 hover:opacity-100">
                <?php if ($brand['logo']): ?>
                <img src="<?= ASSETS_URL ?>/../uploads/brands/<?= $brand['logo'] ?>" alt="<?= $brand['name'] ?>" class="h-10 md:h-12">
                <?php else: ?>
                <span class="text-2xl font-bold text-gray-600"><?= $brand['name'] ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-truck text-accent text-xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Miễn phí vận chuyển</h4>
            <p class="text-sm text-gray-500">Đơn hàng từ 500K</p>
        </div>
        <div class="text-center">
            <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-sync-alt text-accent text-xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Đổi trả miễn phí</h4>
            <p class="text-sm text-gray-500">Trong vòng 30 ngày</p>
        </div>
        <div class="text-center">
            <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-shield-alt text-accent text-xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Bảo hành chính hãng</h4>
            <p class="text-sm text-gray-500">100% authentic</p>
        </div>
        <div class="text-center">
            <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-headset text-accent text-xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Hỗ trợ 24/7</h4>
            <p class="text-sm text-gray-500">Hotline: 1900 xxxx</p>
        </div>
    </div>
</section>
