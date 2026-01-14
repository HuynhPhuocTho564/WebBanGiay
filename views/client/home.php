<!-- Hero Banner Slider -->
<section class="relative overflow-hidden">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 - Flash Sale -->
            <div class="swiper-slide">
                <div class="bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 text-white">
                    <div class="container mx-auto px-4 py-12 md:py-20">
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1 text-center md:text-left">
                                <span class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur rounded-full text-sm mb-4 font-medium">🔥 Hot Deal</span>
                                <h2 class="text-3xl md:text-5xl font-bold mb-4">GIẢM GIÁ<br><span class="text-yellow-300">LÊN ĐẾN 50%</span></h2>
                                <p class="text-white/90 mb-6 max-w-md text-lg">Săn ngay những đôi sneaker hot nhất với giá sốc!</p>
                                <a href="<?= BASE_URL ?>/home/products" class="inline-block px-8 py-3 bg-white text-red-600 hover:bg-yellow-300 rounded-full font-bold transition transform hover:scale-105">
                                    Mua ngay <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                            <div class="flex-1">
                                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600" 
                                     alt="Banner" class="w-full max-w-md mx-auto drop-shadow-2xl transform hover:rotate-6 transition duration-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 2 - Sản phẩm mới -->
            <div class="swiper-slide">
                <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500 text-white">
                    <div class="container mx-auto px-4 py-12 md:py-20">
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1 text-center md:text-left">
                                <span class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur rounded-full text-sm mb-4 font-medium">✨ Mới nhất</span>
                                <h2 class="text-3xl md:text-5xl font-bold mb-4">Sản Phẩm<br><span class="text-pink-300">Mới Về</span></h2>
                                <p class="text-white/90 mb-6 max-w-md text-lg">Cập nhật xu hướng mới nhất!</p>
                                <a href="<?= BASE_URL ?>/home/products" class="inline-block px-8 py-3 bg-white text-purple-600 hover:bg-pink-300 rounded-full font-bold transition transform hover:scale-105">
                                    Khám phá <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                            <div class="flex-1">
                                <img src="https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600" 
                                     alt="Banner" class="w-full max-w-md mx-auto drop-shadow-2xl transform hover:-rotate-6 transition duration-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 3 - Bán chạy -->
            <div class="swiper-slide">
                <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white">
                    <div class="container mx-auto px-4 py-12 md:py-20">
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="flex-1 text-center md:text-left">
                                <span class="inline-block px-4 py-1.5 bg-accent rounded-full text-sm mb-4 font-medium">👟 Best Seller</span>
                                <h2 class="text-3xl md:text-5xl font-bold mb-4">Sneaker<br><span class="text-accent">Bán Chạy Nhất</span></h2>
                                <p class="text-gray-300 mb-6 max-w-md text-lg">Được yêu thích bởi hàng ngàn khách hàng!</p>
                                <a href="<?= BASE_URL ?>/home/products" class="inline-block px-8 py-3 bg-accent hover:bg-red-600 rounded-full font-bold transition transform hover:scale-105">
                                    Xem ngay <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                            <div class="flex-1">
                                <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600" 
                                     alt="Banner" class="w-full max-w-md mx-auto drop-shadow-2xl transform hover:rotate-6 transition duration-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- Categories -->
<section class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?= BASE_URL ?>/home/products?gender=Male" class="group relative overflow-hidden rounded-xl aspect-square shadow-lg">
            <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400" 
                 alt="Nam" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <span class="text-white font-bold text-lg group-hover:text-accent transition">GIÀY NAM</span>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/home/products?gender=Female" class="group relative overflow-hidden rounded-xl aspect-square shadow-lg">
            <img src="https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400" 
                 alt="Nữ" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                <span class="text-white font-bold text-lg group-hover:text-pink-400 transition">GIÀY NỮ</span>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/home/products" class="group relative overflow-hidden rounded-xl aspect-square shadow-lg">
            <img src="https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=400" 
                 alt="Sale" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-red-600/80 to-orange-500/60 flex items-end p-4">
                <span class="text-white font-bold text-lg animate-pulse">🔥 GIẢM GIÁ</span>
            </div>
        </a>
        <a href="<?= BASE_URL ?>/home/products" class="group relative overflow-hidden rounded-xl aspect-square shadow-lg">
            <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400" 
                 alt="New" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/70 to-transparent flex items-end p-4">
                <span class="text-white font-bold text-lg group-hover:text-yellow-300 transition">✨ TẤT CẢ</span>
            </div>
        </a>
    </div>
</section>

<!-- Flash Sale Section - Background đỏ cam -->
<?php if (!empty($saleProducts)): ?>
<section class="bg-gradient-to-r from-red-600 via-orange-500 to-red-600 py-10 relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-32 h-32 bg-yellow-400/20 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-48 h-48 bg-yellow-400/20 rounded-full translate-x-1/2 translate-y-1/2"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-bolt text-yellow-300 text-3xl animate-pulse"></i>
                    <h2 class="text-2xl md:text-3xl font-bold text-white">FLASH SALE</h2>
                </div>
                <!-- Countdown Timer -->
                <div class="flex items-center gap-1 text-sm font-mono bg-white/20 backdrop-blur px-3 py-2 rounded-lg">
                    <span id="countdown-hours" class="bg-white text-red-600 px-2 py-1 rounded font-bold min-w-[36px] text-center">00</span>
                    <span class="text-white font-bold">:</span>
                    <span id="countdown-minutes" class="bg-white text-red-600 px-2 py-1 rounded font-bold min-w-[36px] text-center">00</span>
                    <span class="text-white font-bold">:</span>
                    <span id="countdown-seconds" class="bg-white text-red-600 px-2 py-1 rounded font-bold min-w-[36px] text-center">00</span>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/home/products?sort=sale" class="text-white hover:text-yellow-300 font-medium flex items-center gap-2 transition">
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
<?php endif; ?>


<!-- New Arrivals -->
<section class="container mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
            <span class="w-1 h-8 bg-accent rounded-full"></span>
            SẢN PHẨM MỚI
        </h2>
        <a href="<?= BASE_URL ?>/home/products?sort=newest" class="text-accent hover:text-red-600 font-medium text-sm flex items-center gap-1 transition">
            Xem tất cả <i class="fas fa-chevron-right"></i>
        </a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <?php foreach ($newProducts as $product): ?>
        <?php include BASE_PATH . '/views/components/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- Banner CTA - Gradient màu sắc -->
<section class="container mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-purple-600 via-pink-500 to-red-500 rounded-2xl overflow-hidden shadow-xl">
        <div class="flex flex-col md:flex-row items-center">
            <div class="flex-1 p-8 md:p-12 text-white text-center md:text-left">
                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm mb-3">🎁 Ưu đãi đặc biệt</span>
                <h3 class="text-2xl md:text-4xl font-bold mb-3">Giảm 100K</h3>
                <p class="text-white/90 mb-6 text-lg">Cho đơn hàng đầu tiên khi đăng ký thành viên</p>
                <a href="<?= BASE_URL ?>/auth/register" class="inline-block px-8 py-3 bg-white text-purple-600 hover:bg-yellow-300 hover:text-purple-700 rounded-full font-bold transition transform hover:scale-105">
                    Đăng ký ngay <i class="fas fa-gift ml-2"></i>
                </a>
            </div>
            <div class="flex-1 p-4">
                <img src="https://images.unsplash.com/photo-1556906781-9a412961c28c?w=500" 
                     alt="Promo" class="w-full max-w-sm mx-auto rounded-xl shadow-2xl transform hover:scale-105 transition duration-500">
            </div>
        </div>
    </div>
</section>

<!-- Best Sellers -->
<section class="container mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
            <span class="w-1 h-8 bg-yellow-500 rounded-full"></span>
            BÁN CHẠY NHẤT
        </h2>
        <a href="<?= BASE_URL ?>/home/products?sort=popular" class="text-accent hover:text-red-600 font-medium text-sm flex items-center gap-1 transition">
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
<section class="bg-gradient-to-r from-gray-100 to-gray-200 py-10">
    <div class="container mx-auto px-4">
        <h2 class="text-xl md:text-2xl font-bold text-center mb-8">THƯƠNG HIỆU NỔI BẬT</h2>
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-16">
            <?php foreach ($brands as $brand): ?>
            <a href="<?= BASE_URL ?>/home/products?brand=<?= $brand['id'] ?>" 
               class="grayscale hover:grayscale-0 transition-all duration-300 opacity-60 hover:opacity-100 hover:scale-110">
                <?php if ($brand['logo']): ?>
                <img src="<?= ASSETS_URL ?>/../uploads/brands/<?= $brand['logo'] ?>" alt="<?= $brand['name'] ?>" class="h-10 md:h-12">
                <?php else: ?>
                <span class="text-2xl font-bold text-gray-600 hover:text-accent transition"><?= $brand['name'] ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:scale-110 transition">
                <i class="fas fa-truck text-white text-2xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Miễn phí vận chuyển</h4>
            <p class="text-sm text-gray-500">Đơn hàng từ 500K</p>
        </div>
        <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:scale-110 transition">
                <i class="fas fa-sync-alt text-white text-2xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Đổi trả miễn phí</h4>
            <p class="text-sm text-gray-500">Trong vòng 30 ngày</p>
        </div>
        <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:scale-110 transition">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Bảo hành chính hãng</h4>
            <p class="text-sm text-gray-500">100% authentic</p>
        </div>
        <div class="text-center group">
            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:scale-110 transition">
                <i class="fas fa-headset text-white text-2xl"></i>
            </div>
            <h4 class="font-semibold mb-1">Hỗ trợ 24/7</h4>
            <p class="text-sm text-gray-500">Hotline: 1900 xxxx</p>
        </div>
    </div>
</section>

<!-- Swiper JS for Banner Slider -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
// Hero Swiper
new Swiper('.hero-swiper', {
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    }
});

// Flash Sale Countdown
function initFlashSaleCountdown() {
    const hoursEl = document.getElementById('countdown-hours');
    const minutesEl = document.getElementById('countdown-minutes');
    const secondsEl = document.getElementById('countdown-seconds');
    
    if (!hoursEl) return;

    function getNextSaleEnd() {
        const now = new Date();
        const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
        if (now >= endOfDay) {
            endOfDay.setDate(endOfDay.getDate() + 1);
        }
        return endOfDay;
    }

    let saleEndTime = getNextSaleEnd();

    function updateCountdown() {
        const now = new Date();
        let diff = saleEndTime - now;

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

    updateCountdown();
    setInterval(updateCountdown, 1000);
}

document.addEventListener('DOMContentLoaded', initFlashSaleCountdown);
</script>
