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

    <form action="<?= BASE_URL ?>/checkout/placeOrder" method="POST">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Shipping Info -->
            <div class="flex-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-4">Thông tin giao hàng</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Họ và tên *</label>
                            <input type="text" name="fullname" required value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Số điện thoại *</label>
                            <input type="tel" name="phone_number" required value="<?= $user['phone_number'] ?? '' ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" value="<?= $user['email'] ?? '' ?>"
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">Địa chỉ giao hàng *</label>
                            <textarea name="address" rows="2" required
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

                    <!-- Coupon -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Mã giảm giá</label>
                        <div class="flex gap-2">
                            <input type="text" id="couponInput" placeholder="Nhập mã"
                                   class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-accent">
                            <button type="button" onclick="applyCoupon()" class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">
                                Áp dụng
                            </button>
                        </div>
                        <?php if (!empty($coupons)): ?>
                        <div class="mt-2">
                            <button type="button" onclick="toggleCouponList()" class="text-sm text-accent hover:underline">
                                <i class="fas fa-tags mr-1"></i> Xem mã giảm giá có sẵn
                            </button>
                            <div id="couponList" class="hidden mt-2 space-y-2 max-h-40 overflow-y-auto">
                                <?php foreach ($coupons as $c): ?>
                                <div class="p-2 border rounded-lg text-sm cursor-pointer hover:border-accent" onclick="selectCoupon('<?= $c['code'] ?>')">
                                    <div class="flex justify-between items-center">
                                        <span class="font-mono font-bold text-accent"><?= $c['code'] ?></span>
                                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">
                                            <?= $c['discount_type'] === 'percent' ? '-'.$c['discount_value'].'%' : '-'.number_format($c['discount_value'],0,',','.').'đ' ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Đơn tối thiểu: <?= number_format($c['min_order_value'],0,',','.') ?>đ</p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <p id="couponMessage" class="text-sm mt-2 hidden"></p>
                    </div>

                    <hr class="my-4">

                    <!-- Summary -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tạm tính</span>
                            <span id="subtotal"><?= formatMoney($totalAmount) ?></span>
                        </div>
                        <div class="flex justify-between" id="discountRow" style="display:none">
                            <span class="text-gray-500">Giảm giá</span>
                            <span class="text-green-500" id="discountAmount">-0đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Phí vận chuyển</span>
                            <span class="text-green-500">Miễn phí</span>
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

<script>
const originalTotal = <?= $totalAmount ?>;

function toggleCouponList() {
    document.getElementById('couponList').classList.toggle('hidden');
}

function selectCoupon(code) {
    document.getElementById('couponInput').value = code;
    document.getElementById('couponList').classList.add('hidden');
    applyCoupon();
}

function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    if (!code) return;

    fetch('<?= BASE_URL ?>/checkout/applyCoupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `code=${code}&total=${originalTotal}`
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('couponMessage');
        msg.classList.remove('hidden', 'text-green-500', 'text-red-500');
        
        if (data.success) {
            msg.classList.add('text-green-500');
            msg.textContent = data.message;
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountAmount').textContent = '-' + data.discountFormatted;
            document.getElementById('totalAmount').textContent = data.finalTotalFormatted;
        } else {
            msg.classList.add('text-red-500');
            msg.textContent = data.message;
        }
    });
}
</script>
