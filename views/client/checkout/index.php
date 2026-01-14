<div class="container mx-auto px-4 py-6">
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="<?= BASE_URL ?>" class="hover:text-accent">Trang chủ</a></li>
            <li>/</li>
            <li><a href="<?= BASE_URL ?>/cart" class="hover:text-accent">Giỏ hàng</a></li>
            <li>/</li>
            <li class="text-primary font-medium">Thanh toán</li>
        </ol>
    </nav>

    <h1 class="text-2xl font-bold mb-6">Thanh toán</h1>

    <form action="<?= BASE_URL ?>/checkout/placeOrder" method="POST" id="checkoutForm">
        <input type="hidden" name="coupon_id" id="selectedCouponId" value="">
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Shipping Info -->
            <div class="flex-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-4">Thông tin giao hàng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Họ và tên *</label>
                            <input type="text" name="fullname" required value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                                   oninvalid="this.setCustomValidity('Vui lòng nhập họ và tên')" oninput="this.setCustomValidity('')"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Số điện thoại *</label>
                            <input type="tel" name="phone_number" required value="<?= $user['phone_number'] ?? '' ?>"
                                   minlength="10" maxlength="11" pattern="^0[0-9]{9,10}$"
                                   oninvalid="this.setCustomValidity('Số điện thoại phải bắt đầu bằng 0 và có 10-11 số')" 
                                   oninput="this.setCustomValidity(''); this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                   placeholder="VD: 0901234567"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" value="<?= $user['email'] ?? '' ?>"
                                   oninvalid="this.setCustomValidity('Vui lòng nhập email hợp lệ')" oninput="this.setCustomValidity('')"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Địa chỉ giao hàng *</label>
                            <textarea name="address" rows="2" required
                                      oninvalid="this.setCustomValidity('Vui lòng nhập địa chỉ giao hàng')" oninput="this.setCustomValidity('')"
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent"><?= $user['address'] ?? '' ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Ghi chú</label>
                            <textarea name="note" rows="2" placeholder="Ghi chú về đơn hàng..."
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-4">Phương thức thanh toán</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-accent">
                            <input type="radio" name="payment_method" value="COD" checked class="text-accent">
                            <i class="fas fa-truck text-xl text-gray-500"></i>
                            <div>
                                <p class="font-medium">Thanh toán khi nhận hàng (COD)</p>
                                <p class="text-sm text-gray-500">Thanh toán bằng tiền mặt khi nhận hàng</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-accent">
                            <input type="radio" name="payment_method" value="Banking" class="text-accent">
                            <i class="fas fa-university text-xl text-gray-500"></i>
                            <div>
                                <p class="font-medium">Chuyển khoản ngân hàng</p>
                                <p class="text-sm text-gray-500">Chuyển khoản trước khi giao hàng</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-96">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-lg mb-4">Đơn hàng của bạn</h3>
                    
                    <!-- Products -->
                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="flex gap-3">
                            <img src="<?= productImage($item['thumbnail']) ?>" class="w-16 h-16 object-cover rounded-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium line-clamp-1"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-xs text-gray-500"><?= $item['color'] ?> / <?= $item['size'] ?> x<?= $item['quantity'] ?></p>
                                <p class="text-sm text-accent font-medium"><?= formatMoney($item['subtotal']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-4">

                    <!-- Coupon Section -->
                    <div class="mb-4">
                        <button type="button" onclick="openCouponModal()" 
                                class="w-full flex items-center justify-between p-3 border border-dashed border-accent rounded-lg text-accent hover:bg-red-50 transition">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-ticket-alt"></i>
                                <span id="couponBtnText">Chọn mã giảm giá</span>
                            </span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div id="selectedCouponInfo" class="hidden mt-2 p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-green-700" id="selectedCouponName"></p>
                                    <p class="text-sm text-green-600" id="selectedCouponDiscount"></p>
                                </div>
                                <button type="button" onclick="removeCoupon()" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tạm tính</span>
                            <span id="subtotal"><?= formatMoney($totalAmount) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Phí vận chuyển</span>
                            <span class="text-green-500">Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-green-600" id="discountRow" style="display: none;">
                            <span>Giảm giá</span>
                            <span id="discountAmount">-0đ</span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="flex justify-between font-bold text-lg">
                        <span>Tổng cộng</span>
                        <span class="text-accent" id="totalAmount"><?= formatMoney($totalAmount) ?></span>
                    </div>

                    <button type="submit" class="w-full py-3 bg-accent text-white rounded-lg font-medium hover:bg-red-600 transition mt-6">
                        Đặt hàng
                    </button>

                    <p class="text-xs text-gray-500 text-center mt-4">
                        Bằng việc đặt hàng, bạn đồng ý với <a href="#" class="text-accent">Điều khoản dịch vụ</a>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Coupon Modal -->
<div id="couponModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeCouponModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-xl shadow-xl">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-bold text-lg">Chọn mã giảm giá</h3>
            <button onclick="closeCouponModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Input mã -->
        <div class="p-4 border-b">
            <div class="flex gap-2">
                <input type="text" id="couponCodeInput" placeholder="Nhập mã giảm giá" 
                       class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-accent uppercase">
                <button type="button" onclick="applyCouponCode()" 
                        class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-red-600 transition">
                    Áp dụng
                </button>
            </div>
            <p id="couponError" class="text-red-500 text-sm mt-2 hidden"></p>
        </div>

        <!-- Danh sách voucher -->
        <div class="p-4 max-h-80 overflow-y-auto">
            <p class="text-sm text-gray-500 mb-3">Voucher khả dụng</p>
            <div id="couponList" class="space-y-3">
                <!-- Coupons will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
const subtotalAmount = <?= $totalAmount ?>;
let selectedCoupon = null;

// Load available coupons
async function loadCoupons() {
    try {
        const res = await fetch('<?= BASE_URL ?>/checkout/getAvailableCoupons?total=' + subtotalAmount);
        const data = await res.json();
        
        const list = document.getElementById('couponList');
        if (data.coupons.length === 0) {
            list.innerHTML = '<p class="text-center text-gray-500 py-4">Không có voucher khả dụng</p>';
            return;
        }
        
        list.innerHTML = data.coupons.map(coupon => {
            const isEligible = subtotalAmount >= coupon.min_order_value;
            return `
                <div class="border rounded-lg p-3 ${isEligible ? 'hover:border-accent cursor-pointer' : 'opacity-50'}" 
                     ${isEligible ? `onclick="selectCoupon(${JSON.stringify(coupon).replace(/"/g, '&quot;')})"` : ''}>
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-ticket-alt text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-accent">${coupon.code}</p>
                            <p class="text-sm font-medium">${coupon.name || coupon.code}</p>
                            <p class="text-xs text-gray-500">
                                ${coupon.discount_type === 'percent' 
                                    ? `Giảm ${coupon.discount_value}%${coupon.max_discount ? ' (tối đa ' + formatMoney(coupon.max_discount) + ')' : ''}`
                                    : 'Giảm ' + formatMoney(coupon.discount_value)}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                Đơn tối thiểu: ${formatMoney(coupon.min_order_value)}
                                ${!isEligible ? '<span class="text-red-500 ml-1">(Chưa đủ điều kiện)</span>' : ''}
                            </p>
                        </div>
                        ${isEligible ? '<i class="fas fa-chevron-right text-gray-400"></i>' : ''}
                    </div>
                </div>
            `;
        }).join('');
    } catch (e) {
        console.error(e);
    }
}

function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function openCouponModal() {
    document.getElementById('couponModal').classList.remove('hidden');
    loadCoupons();
}

function closeCouponModal() {
    document.getElementById('couponModal').classList.add('hidden');
    document.getElementById('couponError').classList.add('hidden');
}

function selectCoupon(coupon) {
    selectedCoupon = coupon;
    document.getElementById('selectedCouponId').value = coupon.id;
    
    // Calculate discount
    let discount = 0;
    if (coupon.discount_type === 'percent') {
        discount = subtotalAmount * coupon.discount_value / 100;
        if (coupon.max_discount && discount > coupon.max_discount) {
            discount = coupon.max_discount;
        }
    } else {
        discount = coupon.discount_value;
    }
    
    // Update UI
    document.getElementById('couponBtnText').textContent = coupon.code;
    document.getElementById('selectedCouponInfo').classList.remove('hidden');
    document.getElementById('selectedCouponName').textContent = coupon.name || coupon.code;
    document.getElementById('selectedCouponDiscount').textContent = '-' + formatMoney(discount);
    
    document.getElementById('discountRow').style.display = 'flex';
    document.getElementById('discountAmount').textContent = '-' + formatMoney(discount);
    document.getElementById('totalAmount').textContent = formatMoney(subtotalAmount - discount);
    
    closeCouponModal();
}

function removeCoupon() {
    selectedCoupon = null;
    document.getElementById('selectedCouponId').value = '';
    document.getElementById('couponBtnText').textContent = 'Chọn mã giảm giá';
    document.getElementById('selectedCouponInfo').classList.add('hidden');
    document.getElementById('discountRow').style.display = 'none';
    document.getElementById('totalAmount').textContent = formatMoney(subtotalAmount);
}

async function applyCouponCode() {
    const code = document.getElementById('couponCodeInput').value.trim().toUpperCase();
    if (!code) return;
    
    try {
        const res = await fetch('<?= BASE_URL ?>/checkout/validateCoupon', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `code=${code}&total=${subtotalAmount}`
        });
        const data = await res.json();
        
        if (data.success) {
            selectCoupon(data.coupon);
            document.getElementById('couponCodeInput').value = '';
        } else {
            document.getElementById('couponError').textContent = data.message;
            document.getElementById('couponError').classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
    }
}
</script>
