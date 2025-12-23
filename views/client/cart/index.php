<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="<?= BASE_URL ?>" class="hover:text-accent">Trang chủ</a></li>
            <li>/</li>
            <li class="text-primary font-medium">Giỏ hàng</li>
        </ol>
    </nav>

    <h1 class="text-2xl font-bold mb-6">Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
    <!-- Empty Cart -->
    <div class="text-center py-16 bg-white rounded-xl shadow-sm">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-shopping-cart text-4xl text-gray-300"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Giỏ hàng trống</h2>
        <p class="text-gray-500 mb-6">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
        <a href="<?= BASE_URL ?>/home/products" class="inline-block px-8 py-3 bg-accent text-white rounded-full hover:bg-red-600 transition font-medium">
            <i class="fas fa-shopping-bag mr-2"></i>Khám phá sản phẩm
        </a>
    </div>
    
    <!-- Suggested Products -->
    <?php 
    $suggestedProducts = Database::getInstance()->fetchAll(
        "SELECT p.*, b.name as brand_name FROM products p 
         LEFT JOIN brands b ON p.brand_id = b.id 
         ORDER BY RAND() LIMIT 4"
    );
    if (!empty($suggestedProducts)): 
    ?>
    <section class="mt-12">
        <h2 class="text-xl font-bold mb-6">Có thể bạn sẽ thích</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($suggestedProducts as $product): ?>
            <?php include BASE_PATH . '/views/components/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    <?php else: ?>
    
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Cart Items -->
        <div class="flex-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="hidden md:grid grid-cols-12 gap-4 p-4 bg-gray-50 text-sm font-medium text-gray-600">
                    <div class="col-span-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded">
                            <span>Chọn</span>
                        </label>
                    </div>
                    <div class="col-span-5">Sản phẩm</div>
                    <div class="col-span-2 text-center">Đơn giá</div>
                    <div class="col-span-2 text-center">Số lượng</div>
                    <div class="col-span-2 text-right">Thành tiền</div>
                </div>

                <!-- Items -->
                <div class="divide-y">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="p-4 cart-item" data-variant-id="<?= $item['id'] ?>" data-price="<?= $item['final_price'] ?>" data-quantity="<?= $item['quantity'] ?>">
                        <div class="grid grid-cols-12 gap-4 items-center">
                            <!-- Checkbox -->
                            <div class="col-span-2 md:col-span-1">
                                <input type="checkbox" class="item-checkbox w-5 h-5 rounded text-accent cursor-pointer" 
                                       value="<?= $item['id'] ?>" onchange="updateSelectedTotal()">
                            </div>
                            <!-- Product Info -->
                            <div class="col-span-10 md:col-span-5">
                                <div class="flex gap-4">
                                    <a href="<?= BASE_URL ?>/home/product/<?= $item['slug'] ?>" class="flex-shrink-0">
                                        <img src="<?= productImage($item['thumbnail']) ?>" alt="<?= $item['name'] ?>" 
                                             class="w-20 h-20 object-cover rounded-lg">
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <a href="<?= BASE_URL ?>/home/product/<?= $item['slug'] ?>" 
                                           class="font-medium hover:text-accent line-clamp-2"><?= htmlspecialchars($item['name']) ?></a>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <?= $item['color'] ?> / Size <?= $item['size'] ?>
                                        </p>
                                        <button onclick="removeItem(<?= $item['id'] ?>)" 
                                                class="text-sm text-red-500 hover:underline mt-1 md:hidden">
                                            <i class="fas fa-trash mr-1"></i> Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-span-4 md:col-span-2 text-center">
                                <span class="md:hidden text-sm text-gray-500">Đơn giá: </span>
                                <span class="text-accent font-medium"><?= formatMoney($item['final_price']) ?></span>
                            </div>

                            <!-- Quantity -->
                            <div class="col-span-4 md:col-span-2">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="updateQty(<?= $item['id'] ?>, -1)" 
                                            class="w-8 h-8 border rounded hover:bg-gray-100">-</button>
                                    <input type="number" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>"
                                           class="w-12 h-8 text-center border rounded qty-input"
                                           onchange="updateQtyDirect(<?= $item['id'] ?>, this.value)">
                                    <button onclick="updateQty(<?= $item['id'] ?>, 1)" 
                                            class="w-8 h-8 border rounded hover:bg-gray-100">+</button>
                                </div>
                            </div>

                            <!-- Subtotal -->
                            <div class="col-span-4 md:col-span-2 text-right">
                                <span class="md:hidden text-sm text-gray-500">Tổng: </span>
                                <span class="font-bold text-accent item-subtotal"><?= formatMoney($item['subtotal']) ?></span>
                                <button onclick="removeItem(<?= $item['id'] ?>)" 
                                        class="ml-2 text-gray-400 hover:text-red-500 hidden md:inline-block">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-4">
                <a href="<?= BASE_URL ?>/home/products" class="text-accent hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Tiếp tục mua sắm
                </a>
                <button onclick="clearCart()" class="text-red-500 hover:underline">
                    <i class="fas fa-trash mr-1"></i> Xóa tất cả
                </button>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-80">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                <h3 class="font-bold text-lg mb-4">Tóm tắt đơn hàng</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Đã chọn</span>
                        <span id="summaryCount">0 sản phẩm</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tạm tính</span>
                        <span id="summarySubtotal">0đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phí vận chuyển</span>
                        <span class="text-green-500">Miễn phí</span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="flex justify-between font-bold text-lg">
                    <span>Tổng cộng</span>
                    <span class="text-accent" id="summaryTotal">0đ</span>
                </div>

                <button onclick="proceedToCheckout()" id="checkoutBtn" disabled
                   class="block w-full py-3 bg-gray-300 text-white text-center rounded-lg font-medium cursor-not-allowed transition mt-6">
                    Vui lòng chọn sản phẩm
                </button>

                <!-- Coupon -->
                <div class="mt-4">
                    <div class="flex gap-2">
                        <input type="text" id="couponCode" placeholder="Mã giảm giá" 
                               class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-accent">
                        <button onclick="applyCouponCart()" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">Áp dụng</button>
                    </div>
                    <?php 
                    $availableCoupons = Database::getInstance()->fetchAll(
                        "SELECT * FROM coupons WHERE status = 1 AND start_date <= NOW() AND end_date >= NOW() AND usage_limit > 0 LIMIT 5"
                    );
                    if (!empty($availableCoupons)): 
                    ?>
                    <button type="button" onclick="toggleCartCoupons()" class="text-sm text-accent hover:underline mt-2">
                        <i class="fas fa-tags mr-1"></i> Xem mã giảm giá
                    </button>
                    <div id="cartCouponList" class="hidden mt-2 space-y-2 max-h-40 overflow-y-auto">
                        <?php foreach ($availableCoupons as $c): ?>
                        <div class="p-2 border rounded text-xs cursor-pointer hover:border-accent" onclick="selectCartCoupon('<?= $c['code'] ?>')">
                            <div class="flex justify-between">
                                <span class="font-mono font-bold"><?= $c['code'] ?></span>
                                <span class="text-accent"><?= $c['discount_type'] === 'percent' ? '-'.$c['discount_value'].'%' : '-'.number_format($c['discount_value'],0,',','.').'đ' ?></span>
                            </div>
                            <p class="text-gray-500">Đơn từ <?= number_format($c['min_order_value'],0,',','.') ?>đ</p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <p id="couponMsg" class="text-sm mt-2 hidden"></p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


<script>
// Chọn tất cả
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedTotal();
}

// Cập nhật tổng tiền theo sản phẩm đã chọn
function updateSelectedTotal() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    let total = 0;
    let count = 0;
    
    checkboxes.forEach(cb => {
        const item = cb.closest('.cart-item');
        const price = parseFloat(item.dataset.price);
        const qty = parseInt(item.querySelector('.qty-input').value);
        total += price * qty;
        count += qty;
    });
    
    document.getElementById('summaryCount').textContent = checkboxes.length + ' sản phẩm';
    document.getElementById('summarySubtotal').textContent = formatMoney(total);
    document.getElementById('summaryTotal').textContent = formatMoney(total);
    
    // Cập nhật nút thanh toán
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkboxes.length > 0) {
        checkoutBtn.disabled = false;
        checkoutBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
        checkoutBtn.classList.add('bg-accent', 'hover:bg-red-600', 'cursor-pointer');
        checkoutBtn.textContent = 'Tiến hành thanh toán';
    } else {
        checkoutBtn.disabled = true;
        checkoutBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
        checkoutBtn.classList.remove('bg-accent', 'hover:bg-red-600', 'cursor-pointer');
        checkoutBtn.textContent = 'Vui lòng chọn sản phẩm';
    }
    
    // Cập nhật trạng thái "Chọn tất cả"
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    document.getElementById('selectAll').checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
}

// Format tiền
function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

// Tiến hành thanh toán với sản phẩm đã chọn
function proceedToCheckout() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkboxes.length === 0) {
        showToast('Vui lòng chọn ít nhất một sản phẩm để thanh toán', 'error');
        return;
    }
    showLoading();
    
    const selectedIds = Array.from(checkboxes).map(cb => cb.value);
    
    // Gửi danh sách đã chọn lên server
    fetch('<?= BASE_URL ?>/checkout/setSelectedItems', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'items=' + encodeURIComponent(JSON.stringify(selectedIds))
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '<?= BASE_URL ?>/checkout';
        }
    });
}

// Cập nhật số lượng
function updateQty(variantId, delta) {
    const input = document.querySelector(`.cart-item[data-variant-id="${variantId}"] .qty-input`);
    let newQty = parseInt(input.value) + delta;
    if (newQty < 1) newQty = 1;
    if (newQty > parseInt(input.max)) newQty = parseInt(input.max);
    input.value = newQty;
    updateQtyDirect(variantId, newQty);
}

function updateQtyDirect(variantId, quantity) {
    fetch('<?= BASE_URL ?>/cart/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `variant_id=${variantId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cartCount').textContent = data.cartCount;
            // Cập nhật lại tổng tiền đã chọn
            updateSelectedTotal();
            // Reload để cập nhật subtotal từng item
            if (quantity <= 0) location.reload();
        } else {
            showToast(data.message, 'error');
        }
    });
}

// Xóa sản phẩm
function removeItem(variantId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    showLoading();
    
    fetch('<?= BASE_URL ?>/cart/remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `variant_id=${variantId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cartCount').textContent = data.cartCount;
            showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
            setTimeout(() => location.reload(), 500);
        }
    });
}

// Xóa tất cả
function clearCart() {
    if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) return;
    showLoading();
    window.location.href = '<?= BASE_URL ?>/cart/clear';
}

// Coupon functions
function toggleCartCoupons() {
    document.getElementById('cartCouponList').classList.toggle('hidden');
}

function selectCartCoupon(code) {
    document.getElementById('couponCode').value = code;
    document.getElementById('cartCouponList').classList.add('hidden');
}

function applyCouponCart() {
    const code = document.getElementById('couponCode').value.trim();
    if (!code) {
        showToast('Vui lòng nhập mã giảm giá', 'error');
        return;
    }
    // Lưu mã và chuyển đến checkout
    sessionStorage.setItem('couponCode', code);
    window.location.href = '<?= BASE_URL ?>/checkout';
}
</script>
