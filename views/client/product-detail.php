<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500 flex-wrap">
            <li><a href="<?= BASE_URL ?>" class="hover:text-accent">Trang chủ</a></li>
            <li>/</li>
            <li><a href="<?= BASE_URL ?>/home/products" class="hover:text-accent">Sản phẩm</a></li>
            <li>/</li>
            <li class="text-primary font-medium truncate max-w-[200px]"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Gallery -->
        <div class="space-y-4">
            <!-- Main Image with Magnifier -->
            <div id="imageContainer" class="aspect-square bg-gray-100 rounded-2xl overflow-hidden relative">
                <img id="mainImage" src="<?= productImage($product['thumbnail']) ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="w-full h-full object-cover">
                <!-- Magnifier Glass -->
                <div id="magnifier" class="hidden absolute w-[150px] h-[150px] rounded-full border-4 border-white shadow-xl pointer-events-none"
                     style="background-repeat: no-repeat;"></div>
            </div>
            
            <!-- Thumbnails -->
            <?php if (!empty($gallery)): ?>
            <div class="flex gap-2 overflow-x-auto pb-2">
                <button onclick="changeImage('<?= productImage($product['thumbnail']) ?>')"
                        class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-primary">
                    <img src="<?= productImage($product['thumbnail']) ?>" alt="" class="w-full h-full object-cover">
                </button>
                <?php foreach ($gallery as $img): ?>
                <button onclick="changeImage('<?= productImage($img['image_path']) ?>')"
                        class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition">
                    <img src="<?= productImage($img['image_path']) ?>" alt="" class="w-full h-full object-cover">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Video -->
            <?php if (!empty($product['video_url'])): ?>
            <div class="aspect-video rounded-xl overflow-hidden">
                <iframe src="<?= $product['video_url'] ?>" class="w-full h-full" allowfullscreen></iframe>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div>
            <!-- Brand -->
            <?php if (!empty($product['brand_name'])): ?>
            <p class="text-sm text-gray-500 uppercase tracking-wide mb-2"><?= $product['brand_name'] ?></p>
            <?php endif; ?>

            <!-- Name -->
            <h1 class="text-2xl md:text-3xl font-bold mb-4"><?= htmlspecialchars($product['name']) ?></h1>

            <!-- Rating & Views -->
            <div class="flex items-center gap-4 mb-4 text-sm">
                <div class="flex items-center gap-1 text-yellow-400">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                    <span class="text-gray-600 ml-1">4.5</span>
                </div>
                <span class="text-gray-400">|</span>
                <span class="text-gray-500"><i class="far fa-eye mr-1"></i> <?= number_format($product['views']) ?> lượt xem</span>
            </div>

            <!-- Price -->
            <?php 
            $hasDiscount = $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
            $finalPrice = $hasDiscount ? $product['discount_price'] : $product['price'];
            ?>
            <div class="flex items-center gap-3 mb-6">
                <span class="text-3xl font-bold text-accent"><?= formatMoney($finalPrice) ?></span>
                <?php if ($hasDiscount): ?>
                <span class="text-xl text-gray-400 line-through"><?= formatMoney($product['price']) ?></span>
                <span class="px-2 py-1 bg-accent text-white text-sm rounded">
                    -<?= discountPercent($product['price'], $product['discount_price']) ?>%
                </span>
                <?php endif; ?>
            </div>

            <!-- Add to Cart Form -->
            <form id="addToCartForm" class="space-y-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <!-- Color Selection -->
                <?php 
                $colors = array_unique(array_column($variants, 'color'));
                $hasVariants = !empty($variants);
                if (!empty($colors)):
                ?>
                <div>
                    <label class="block font-medium mb-2">Màu sắc: <span id="selectedColor" class="text-accent"></span></label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($colors as $color): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="<?= $color ?>" class="hidden peer"
                                   onchange="updateVariant()">
                            <span class="inline-block px-4 py-2 border-2 rounded-lg text-sm peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white transition">
                                <?= $color ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Size Selection -->
                <?php 
                $sizes = array_unique(array_column($variants, 'size'));
                sort($sizes);
                if (!empty($sizes)):
                ?>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="font-medium">Size: <span id="selectedSize" class="text-accent"></span></label>
                        <button type="button" onclick="openSizeGuide()" class="text-sm text-accent hover:underline">
                            <i class="fas fa-ruler mr-1"></i> Hướng dẫn chọn size
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2" id="sizeOptions">
                        <?php foreach ($sizes as $size): ?>
                        <label class="cursor-pointer size-option" data-size="<?= $size ?>">
                            <input type="radio" name="size" value="<?= $size ?>" class="hidden peer"
                                   onchange="updateVariant()">
                            <span class="inline-flex items-center justify-center w-12 h-12 border-2 rounded-lg text-sm font-medium peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white peer-disabled:bg-gray-100 peer-disabled:text-gray-400 peer-disabled:cursor-not-allowed transition">
                                <?= $size ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quantity -->
                <div>
                    <label class="block font-medium mb-2">Số lượng</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="changeQty(-1)" 
                                class="w-10 h-10 border rounded-lg hover:bg-gray-100 transition">-</button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="99"
                               class="w-20 h-10 text-center border rounded-lg focus:outline-none focus:border-accent">
                        <button type="button" onclick="changeQty(1)" 
                                class="w-10 h-10 border rounded-lg hover:bg-gray-100 transition">+</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" id="addToCartBtn"
                            class="flex-1 py-3 bg-primary text-white rounded-xl font-medium hover:bg-accent transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                        <i class="fas fa-shopping-bag mr-2"></i> Thêm vào giỏ
                    </button>
                    <button type="button" onclick="buyNow()"
                            class="flex-1 py-3 bg-accent text-white rounded-xl font-medium hover:bg-red-600 transition">
                        Mua ngay
                    </button>
                    <?php if (Session::isLoggedIn()): ?>
                    <button type="button" onclick="addToWishlist(<?= $product['id'] ?>)"
                            class="w-12 h-12 border-2 rounded-xl hover:border-accent hover:text-accent transition">
                        <i class="far fa-heart"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Policies -->
            <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t">
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-truck text-accent text-lg"></i>
                    <div>
                        <p class="font-medium">Miễn phí vận chuyển</p>
                        <p class="text-gray-500">Đơn từ 500K</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-sync-alt text-accent text-lg"></i>
                    <div>
                        <p class="font-medium">Đổi trả miễn phí</p>
                        <p class="text-gray-500">Trong 30 ngày</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-shield-alt text-accent text-lg"></i>
                    <div>
                        <p class="font-medium">Bảo hành chính hãng</p>
                        <p class="text-gray-500">12 tháng</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <i class="fas fa-check-circle text-accent text-lg"></i>
                    <div>
                        <p class="font-medium">100% Authentic</p>
                        <p class="text-gray-500">Cam kết chính hãng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description Tabs -->
    <div class="bg-white rounded-2xl shadow-sm mb-12">
        <div class="border-b">
            <div class="flex gap-6 px-6 overflow-x-auto">
                <button onclick="showTab('description')" class="tab-btn py-4 border-b-2 border-primary font-medium whitespace-nowrap" data-tab="description">
                    Mô tả sản phẩm
                </button>
                <button onclick="showTab('reviews')" class="tab-btn py-4 border-b-2 border-transparent text-gray-500 hover:text-primary whitespace-nowrap" data-tab="reviews">
                    Đánh giá (<?= $reviewCount ?? 0 ?>)
                </button>
                <button onclick="showTab('policy')" class="tab-btn py-4 border-b-2 border-transparent text-gray-500 hover:text-primary whitespace-nowrap" data-tab="policy">
                    Chính sách đổi trả
                </button>
            </div>
        </div>
        <div class="p-6">
            <!-- Tab Mô tả -->
            <div id="tab-description" class="tab-content prose max-w-none">
                <?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả')) ?>
            </div>

            <!-- Tab Đánh giá -->
            <div id="tab-reviews" class="tab-content hidden">
                <!-- Rating Summary -->
                <div class="flex flex-col md:flex-row gap-8 mb-8 pb-8 border-b">
                    <div class="text-center md:w-48">
                        <div class="text-5xl font-bold text-accent"><?= number_format($avgRating ?? 4.5, 1) ?></div>
                        <div class="flex justify-center gap-1 my-2 text-yellow-400">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?= $i <= round($avgRating ?? 4.5) ? '' : '-half-alt' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="text-gray-500"><?= $reviewCount ?? 0 ?> đánh giá</p>
                    </div>
                    <div class="flex-1 space-y-2">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <div class="flex items-center gap-3">
                            <span class="w-8 text-sm"><?= $i ?> <i class="fas fa-star text-yellow-400 text-xs"></i></span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full" style="width: <?= rand(10, 90) ?>%"></div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Write Review -->
                <?php if (Session::isLoggedIn()): ?>
                <div class="mb-8 p-4 bg-gray-50 rounded-xl">
                    <h4 class="font-bold mb-4">Viết đánh giá của bạn</h4>
                    <form id="reviewForm" action="javascript:void(0)" onsubmit="submitReview(event)">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Đánh giá</label>
                            <div class="flex gap-2" id="ratingStars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="button" onclick="setRating(<?= $i ?>)" class="text-2xl text-gray-300 hover:text-yellow-400 transition">
                                    <i class="fas fa-star"></i>
                                </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="5">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Nhận xét</label>
                            <textarea name="comment" id="reviewComment" rows="3" required placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent"></textarea>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600">
                            Gửi đánh giá
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="mb-8 p-4 bg-gray-50 rounded-xl text-center">
                    <p class="text-gray-500 mb-2">Đăng nhập để viết đánh giá</p>
                    <a href="<?= BASE_URL ?>/auth/login" class="text-accent hover:underline">Đăng nhập ngay</a>
                </div>
                <?php endif; ?>

                <!-- Reviews List -->
                <div class="space-y-6">
                    <?php if (empty($reviews)): ?>
                    <p class="text-center text-gray-500 py-8">Chưa có đánh giá nào cho sản phẩm này</p>
                    <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="flex gap-4 pb-6 border-b last:border-0">
                        <img src="<?= avatar($review['avatar'] ?? null) ?>" alt="" class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium"><?= htmlspecialchars($review['fullname']) ?></span>
                                <span class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($review['created_at'])) ?></span>
                            </div>
                            <div class="flex gap-0.5 text-yellow-400 text-sm mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= $review['rating'] ? '' : ' text-gray-300' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-gray-600"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab Chính sách -->
            <div id="tab-policy" class="tab-content hidden">
                <h4 class="font-bold mb-3">Chính sách đổi trả</h4>
                <ul class="list-disc list-inside space-y-2 text-gray-600">
                    <li>Đổi trả miễn phí trong vòng 30 ngày kể từ ngày nhận hàng</li>
                    <li>Sản phẩm còn nguyên tem, nhãn mác, chưa qua sử dụng</li>
                    <li>Hỗ trợ đổi size miễn phí nếu không vừa</li>
                    <li>Hoàn tiền 100% nếu phát hiện hàng giả</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <section>
        <h2 class="text-xl font-bold mb-6">SẢN PHẨM LIÊN QUAN</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($relatedProducts as $product): ?>
            <?php include BASE_PATH . '/views/components/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- Lightbox Modal (Zoom ảnh) -->
<div id="lightboxModal" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300 z-10">&times;</button>
    <button onclick="zoomIn()" class="absolute top-4 left-4 text-white text-xl hover:text-gray-300 z-10 px-3 py-1 border border-white/50 rounded">
        <i class="fas fa-search-plus"></i>
    </button>
    <button onclick="zoomOut()" class="absolute top-4 left-20 text-white text-xl hover:text-gray-300 z-10 px-3 py-1 border border-white/50 rounded">
        <i class="fas fa-search-minus"></i>
    </button>
    <div id="lightboxContainer" class="overflow-auto max-w-full max-h-full cursor-grab active:cursor-grabbing">
        <img id="lightboxImage" src="" alt="" class="transition-transform duration-200" style="transform-origin: center center;">
    </div>
</div>

<!-- Size Guide Modal -->
<div id="sizeGuideModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeSizeGuide()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold">Hướng dẫn chọn size</h3>
            <button onclick="closeSizeGuide()" class="text-2xl">&times;</button>
        </div>
        
        <!-- Hướng dẫn đo chân -->
        <div class="mb-4 p-3 bg-blue-50 rounded-lg text-sm">
            <p class="font-medium text-blue-800 mb-2"><i class="fas fa-info-circle mr-1"></i> Cách đo chân:</p>
            <ol class="list-decimal list-inside text-blue-700 space-y-1">
                <li>Đặt chân lên tờ giấy, đánh dấu điểm gót và ngón dài nhất</li>
                <li>Đo khoảng cách giữa 2 điểm (cm)</li>
                <li>Nên đo vào buổi chiều khi chân hơi nở</li>
            </ol>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-center">Size</th>
                    <th class="p-3 text-center">Chiều dài bàn chân (cm)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b"><td class="p-3 text-center font-medium">36</td><td class="p-3 text-center">22.5 - 23</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">37</td><td class="p-3 text-center">23 - 23.5</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">38</td><td class="p-3 text-center">23.5 - 24</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">39</td><td class="p-3 text-center">24 - 24.5</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">40</td><td class="p-3 text-center">24.5 - 25</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">41</td><td class="p-3 text-center">25 - 25.5</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">42</td><td class="p-3 text-center">25.5 - 26</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">43</td><td class="p-3 text-center">26 - 26.5</td></tr>
                <tr class="border-b"><td class="p-3 text-center font-medium">44</td><td class="p-3 text-center">26.5 - 27</td></tr>
                <tr><td class="p-3 text-center font-medium">45</td><td class="p-3 text-center">27 - 27.5</td></tr>
            </tbody>
        </table>

        <div class="mt-4 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800">
            <p><i class="fas fa-lightbulb mr-1"></i> <strong>Lưu ý:</strong> Nếu chân bạn ở giữa 2 size, nên chọn size lớn hơn để thoải mái hơn.</p>
        </div>
    </div>
</div>

<script>
// Variants data
const variants = <?= json_encode($variants) ?>;
let selectedVariant = null;

function changeImage(src) {
    document.getElementById('mainImage').src = src;
}

function changeQty(delta) {
    const input = document.getElementById('quantity');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 99) val = 99;
    input.value = val;
}

function updateVariant() {
    const color = document.querySelector('input[name="color"]:checked')?.value;
    const size = document.querySelector('input[name="size"]:checked')?.value;
    
    document.getElementById('selectedColor').textContent = color || '';
    document.getElementById('selectedSize').textContent = size || '';
    
    if (color && size) {
        selectedVariant = variants.find(v => v.color === color && v.size === size);
        const addBtn = document.getElementById('addToCartBtn');
        
        if (selectedVariant) {
            if (selectedVariant.stock_quantity > 0) {
                addBtn.disabled = false;
            } else {
                addBtn.disabled = true;
            }
        }
    }
}

function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-primary', 'font-medium');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.querySelector(`[data-tab="${tab}"]`).classList.add('border-primary', 'font-medium');
    document.querySelector(`[data-tab="${tab}"]`).classList.remove('border-transparent', 'text-gray-500');
}

function openSizeGuide() {
    document.getElementById('sizeGuideModal').classList.remove('hidden');
}

function closeSizeGuide() {
    document.getElementById('sizeGuideModal').classList.add('hidden');
}

// Add to cart
document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Kiểm tra nếu có variants thì phải chọn
    const hasVariants = variants && variants.length > 0;
    if (hasVariants && !selectedVariant) {
        showToast('Vui lòng chọn màu sắc và size', 'error');
        return;
    }
    
    const quantity = document.getElementById('quantity').value;
    const variantId = selectedVariant ? selectedVariant.id : (variants[0]?.id || 0);
    
    if (!variantId) {
        showToast('Sản phẩm không có sẵn', 'error');
        return;
    }
    
    showLoading();
    
    fetch('<?= BASE_URL ?>/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `variant_id=${variantId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        hideLoading();
        if (data.requireLogin) {
            showToast(data.message, 'info');
            setTimeout(() => window.location.href = '<?= BASE_URL ?>/auth/login', 1500);
            return;
        }
        if (data.success) {
            document.getElementById('cartCount').textContent = data.cartCount;
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(err => {
        hideLoading();
        showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
    });
});

function buyNow() {
    // Kiểm tra nếu có variants thì phải chọn
    const hasVariants = variants && variants.length > 0;
    if (hasVariants && !selectedVariant) {
        showToast('Vui lòng chọn màu sắc và size', 'error');
        return;
    }
    
    const quantity = document.getElementById('quantity').value;
    const variantId = selectedVariant ? selectedVariant.id : (variants[0]?.id || 0);
    
    if (!variantId) {
        showToast('Sản phẩm không có sẵn', 'error');
        return;
    }
    
    showLoading();
    
    fetch('<?= BASE_URL ?>/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `variant_id=${variantId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.requireLogin) {
            hideLoading();
            showToast(data.message, 'info');
            setTimeout(() => window.location.href = '<?= BASE_URL ?>/auth/login', 1500);
            return;
        }
        if (data.success) {
            window.location.href = '<?= BASE_URL ?>/checkout';
        } else {
            hideLoading();
            showToast(data.message, 'error');
        }
    });
}

// Rating stars
function setRating(rating) {
    const ratingInput = document.getElementById('ratingInput');
    const stars = document.querySelectorAll('#ratingStars button');
    
    // Kiểm tra element tồn tại trước khi set
    if (!ratingInput || !stars.length) return;
    
    ratingInput.value = rating;
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}
// Set default 5 stars (chỉ khi đã đăng nhập)
if (document.getElementById('ratingInput')) {
    setRating(5);
}

// Wishlist
function addToWishlist(productId) {
    fetch('<?= BASE_URL ?>/profile/toggleWishlist', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Có lỗi xảy ra', 'error');
        }
    });
}

// Lightbox zoom
let currentZoom = 1;
const minZoom = 1;
const maxZoom = 4;

function openLightbox(src) {
    currentZoom = 1;
    const modal = document.getElementById('lightboxModal');
    const img = document.getElementById('lightboxImage');
    img.src = src;
    img.style.transform = `scale(${currentZoom})`;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightboxModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function zoomIn() {
    if (currentZoom < maxZoom) {
        currentZoom += 0.5;
        document.getElementById('lightboxImage').style.transform = `scale(${currentZoom})`;
    }
}

function zoomOut() {
    if (currentZoom > minZoom) {
        currentZoom -= 0.5;
        document.getElementById('lightboxImage').style.transform = `scale(${currentZoom})`;
    }
}

// Close lightbox on click outside image
document.getElementById('lightboxModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLightbox();
    }
});

// Keyboard controls
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('lightboxModal');
    if (!modal.classList.contains('hidden')) {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === '+' || e.key === '=') zoomIn();
        if (e.key === '-') zoomOut();
    }
});

// Mouse wheel zoom
document.getElementById('lightboxContainer')?.addEventListener('wheel', function(e) {
    e.preventDefault();
    if (e.deltaY < 0) {
        zoomIn();
    } else {
        zoomOut();
    }
});

// Magnifier Glass Effect
const imageContainer = document.getElementById('imageContainer');
const mainImage = document.getElementById('mainImage');
const magnifier = document.getElementById('magnifier');
const zoomLevel = 2.5; // Độ phóng đại
const magnifierSize = 150; // Kích thước kính lúp (px) ~ 2cm

imageContainer?.addEventListener('mouseenter', function() {
    magnifier.classList.remove('hidden');
    magnifier.style.backgroundImage = `url('${mainImage.src}')`;
});

imageContainer?.addEventListener('mouseleave', function() {
    magnifier.classList.add('hidden');
});

imageContainer?.addEventListener('mousemove', function(e) {
    const rect = imageContainer.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    
    // Vị trí kính lúp (căn giữa theo con trỏ)
    let magX = x - magnifierSize / 2;
    let magY = y - magnifierSize / 2;
    
    // Giới hạn trong container
    magX = Math.max(0, Math.min(magX, rect.width - magnifierSize));
    magY = Math.max(0, Math.min(magY, rect.height - magnifierSize));
    
    magnifier.style.left = magX + 'px';
    magnifier.style.top = magY + 'px';
    
    // Tính toán background position để zoom đúng vị trí
    const bgWidth = rect.width * zoomLevel;
    const bgHeight = rect.height * zoomLevel;
    const bgX = -(x * zoomLevel - magnifierSize / 2);
    const bgY = -(y * zoomLevel - magnifierSize / 2);
    
    magnifier.style.backgroundSize = `${bgWidth}px ${bgHeight}px`;
    magnifier.style.backgroundPosition = `${bgX}px ${bgY}px`;
});

// Review form submit function
function submitReview(e) {
    e.preventDefault();
    
    const form = document.getElementById('reviewForm');
    const productId = form.querySelector('input[name="product_id"]').value;
    const rating = document.getElementById('ratingInput').value;
    const comment = document.getElementById('reviewComment').value.trim();
    
    if (!comment) {
        if (typeof showToast === 'function') {
            showToast('Vui lòng nhập nhận xét', 'error');
        } else {
            alert('Vui lòng nhập nhận xét');
        }
        return;
    }
    
    // Disable button while submitting
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang gửi...';
    
    fetch('<?= BASE_URL ?>/home/addReview', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&rating=${rating}&comment=${encodeURIComponent(comment)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reload trang để hiển thị review mới (không hiện toast)
            location.reload();
        } else if (data.requireLogin) {
            window.location.href = '<?= BASE_URL ?>/auth/login';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            if (typeof showToast === 'function') {
                showToast(data.message || 'Có lỗi xảy ra', 'error');
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        }
    })
    .catch(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (typeof showToast === 'function') {
            showToast('Có lỗi xảy ra', 'error');
        } else {
            alert('Có lỗi xảy ra');
        }
    });
}

// Remove old event listener (keep for backward compatibility)
document.getElementById('reviewForm')?.addEventListener('submit', submitReview);
</script>
