</main>

<!-- Footer -->
<footer class="bg-primary text-white mt-12">
    <!-- Main Footer -->
    <div class="container mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-shoe-prints text-accent"></i> SNEAKER STORE
                </h3>
                <p class="text-gray-400 text-sm mb-4">
                    Cửa hàng giày sneaker chính hãng với đa dạng mẫu mã từ các thương hiệu nổi tiếng.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-bold mb-4">Liên kết nhanh</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="<?= BASE_URL ?>/home/products" class="hover:text-white transition">Sản phẩm</a></li>
                    <li><a href="<?= BASE_URL ?>/home/products?gender=Male" class="hover:text-white transition">Giày Nam</a></li>
                    <li><a href="<?= BASE_URL ?>/home/products?gender=Female" class="hover:text-white transition">Giày Nữ</a></li>
                    <li><a href="<?= BASE_URL ?>/home/products?sort=sale" class="hover:text-white transition">Khuyến mãi</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-lg font-bold mb-4">Hỗ trợ</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="<?= BASE_URL ?>/home/guide" class="hover:text-white transition">Hướng dẫn mua hàng</a></li>
                    <li><a href="<?= BASE_URL ?>/home/returnPolicy" class="hover:text-white transition">Chính sách đổi trả</a></li>
                    <li><a href="<?= BASE_URL ?>/home/warranty" class="hover:text-white transition">Chính sách bảo hành</a></li>
                    <li><a href="<?= BASE_URL ?>/home/sizeGuide" class="hover:text-white transition">Hướng dẫn chọn size</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-bold mb-4">Liên hệ</h3>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt mt-1 text-accent"></i>
                        <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone-alt text-accent"></i>
                        <span>1900 xxxx</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-accent"></i>
                        <span>support@sneaker.com</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-clock text-accent"></i>
                        <span>8:00 - 22:00 (T2 - CN)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Newsletter -->
    <div class="border-t border-white/10">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold">Đăng ký nhận tin</h4>
                    <p class="text-sm text-gray-400">Nhận thông tin khuyến mãi mới nhất</p>
                </div>
                <form class="flex w-full md:w-auto">
                    <input type="email" placeholder="Email của bạn" 
                           class="px-4 py-2.5 rounded-l-lg text-gray-900 w-full md:w-64 focus:outline-none">
                    <button type="submit" class="px-6 py-2.5 bg-accent rounded-r-lg hover:bg-red-600 transition font-medium">
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-white/10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-2 text-sm text-gray-400">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa" class="h-6 object-contain">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" class="h-6 object-contain">
                    <span class="text-xs">COD</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-accent text-white rounded-full shadow-lg hidden hover:bg-red-600 transition z-30" aria-label="Về đầu trang">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center">
    <div class="bg-white rounded-xl p-6 flex flex-col items-center gap-3">
        <div class="w-10 h-10 border-4 border-accent border-t-transparent rounded-full animate-spin"></div>
        <span class="text-sm text-gray-600">Đang xử lý...</span>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-[101] space-y-2"></div>

<!-- Scripts -->
<script>
// Mobile Menu
function openMobileMenu() {
    document.getElementById('mobileMenu').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('mobileMenuContent').style.transform = 'translateX(0)';
    }, 10);
}
function closeMobileMenu() {
    document.getElementById('mobileMenuContent').style.transform = 'translateX(-100%)';
    setTimeout(() => {
        document.getElementById('mobileMenu').classList.add('hidden');
    }, 300);
}
document.getElementById('mobileMenuBtn')?.addEventListener('click', openMobileMenu);

// Mobile Search
document.getElementById('searchMobileBtn')?.addEventListener('click', () => {
    document.getElementById('mobileSearch').classList.remove('hidden');
});
function closeMobileSearch() {
    document.getElementById('mobileSearch').classList.add('hidden');
}

// Search Autocomplete
let searchTimeout;
const searchInput = document.getElementById('searchInput');
const searchSuggestions = document.getElementById('searchSuggestions');

searchInput?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        searchSuggestions.classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`<?= BASE_URL ?>/home/searchSuggest?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    searchSuggestions.innerHTML = data.map(item => `
                        <a href="<?= BASE_URL ?>/home/product/${item.slug}" class="flex items-center gap-3 p-3 hover:bg-gray-50 border-b last:border-0">
                            <img src="${item.thumbnail}" alt="" class="w-10 h-10 object-cover rounded">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">${item.name}</p>
                                <p class="text-xs text-accent">${item.price}</p>
                            </div>
                        </a>
                    `).join('') + `
                        <a href="<?= BASE_URL ?>/home/products?q=${encodeURIComponent(query)}" class="block p-3 text-center text-sm text-accent hover:bg-gray-50">
                            Xem tất cả kết quả <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    `;
                    searchSuggestions.classList.remove('hidden');
                } else {
                    searchSuggestions.innerHTML = '<p class="p-3 text-sm text-gray-500 text-center">Không tìm thấy sản phẩm</p>';
                    searchSuggestions.classList.remove('hidden');
                }
            });
    }, 300);
});

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#searchForm')) {
        searchSuggestions?.classList.add('hidden');
    }
});

// Show suggestions on focus if has value
searchInput?.addEventListener('focus', function() {
    if (this.value.trim().length >= 2 && searchSuggestions.innerHTML) {
        searchSuggestions.classList.remove('hidden');
    }
});

// Back to Top
window.addEventListener('scroll', () => {
    const btn = document.getElementById('backToTop');
    if (window.scrollY > 300) {
        btn.classList.remove('hidden');
    } else {
        btn.classList.add('hidden');
    }
});
document.getElementById('backToTop')?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Loading Overlay Functions
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.add('hidden');
    overlay.classList.remove('flex');
}

// Toast Notification Function
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    const bgColor = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
    
    toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg ${bgColor} text-white animate-slide-in`;
    toast.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}
</script>

<style>
@keyframes slide-in {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.animate-slide-in { animation: slide-in 0.3s ease-out; }
</style>

</body>
</html>
