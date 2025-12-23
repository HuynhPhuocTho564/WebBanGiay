<?php
/**
 * Trang chi tiết đơn hàng
 */

function getOrderStatusBadge($status) {
    $badges = [
        'pending' => ['bg-yellow-100 text-yellow-800', 'Chờ xác nhận'],
        'processing' => ['bg-blue-100 text-blue-800', 'Đang xử lý'],
        'shipping' => ['bg-purple-100 text-purple-800', 'Đang giao'],
        'completed' => ['bg-green-100 text-green-800', 'Hoàn thành'],
        'cancelled' => ['bg-red-100 text-red-800', 'Đã hủy'],
        'returning' => ['bg-orange-100 text-orange-800', 'Đang đổi trả'],
        'returned' => ['bg-gray-100 text-gray-800', 'Đã đổi trả'],
    ];
    $badge = $badges[$status] ?? ['bg-gray-100 text-gray-800', $status];
    return '<span class="px-3 py-1 text-sm font-medium rounded-full ' . $badge[0] . '">' . $badge[1] . '</span>';
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/views/client/profile/_sidebar.php'; ?>

        <!-- Content -->
        <div class="flex-1">
            <!-- Back button -->
            <a href="<?= BASE_URL ?>/profile/orders" class="inline-flex items-center gap-2 text-gray-600 hover:text-accent mb-4">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại danh sách đơn hàng</span>
            </a>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b">
                    <div>
                        <h2 class="text-xl font-bold">Đơn hàng #<?= $order['id'] ?></h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Đặt ngày <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <?= getOrderStatusBadge($order['status']) ?>
                        <?php if ($order['status'] === 'pending'): ?>
                        <button onclick="cancelOrder(<?= $order['id'] ?>)" 
                                class="px-4 py-1.5 text-sm border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition">
                            <i class="fas fa-times mr-1"></i>Hủy đơn
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6 border-b">
                    <div>
                        <h3 class="font-medium mb-3">Thông tin nhận hàng</h3>
                        <div class="text-sm space-y-2 text-gray-600">
                            <p><i class="fas fa-user w-5"></i> <?= htmlspecialchars($order['fullname']) ?></p>
                            <p><i class="fas fa-phone w-5"></i> <?= $order['phone_number'] ?></p>
                            <p><i class="fas fa-envelope w-5"></i> <?= $order['email'] ?></p>
                            <p><i class="fas fa-map-marker-alt w-5"></i> <?= htmlspecialchars($order['address']) ?></p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium mb-3">Thông tin thanh toán</h3>
                        <div class="text-sm space-y-2 text-gray-600">
                            <p><i class="fas fa-credit-card w-5"></i> <?= $order['payment_method'] === 'COD' ? 'Thanh toán khi nhận hàng' : $order['payment_method'] ?></p>
                            <?php if (!empty($order['note'])): ?>
                            <p><i class="fas fa-sticky-note w-5"></i> <?= htmlspecialchars($order['note']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="py-6">
                    <h3 class="font-medium mb-4">Sản phẩm đã đặt</h3>
                    <div class="space-y-4">
                        <?php foreach ($orderItems as $item): ?>
                        <div class="flex gap-4 p-3 bg-gray-50 rounded-lg">
                            <?php 
                            $imgSrc = $item['thumbnail'];
                            if (!filter_var($imgSrc, FILTER_VALIDATE_URL)) {
                                $imgSrc = ASSETS_URL . '/images/products/' . $imgSrc;
                            }
                            ?>
                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                                 class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <a href="<?= BASE_URL ?>/product/<?= $item['slug'] ?>" class="font-medium hover:text-accent">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    Size: <?= $item['size'] ?> | Màu: <?= $item['color'] ?>
                                </p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-600">x<?= $item['quantity'] ?></span>
                                    <span class="font-medium text-accent"><?= number_format($item['total_item_price'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Total -->
                <div class="pt-4 border-t">
                    <div class="flex justify-end">
                        <div class="w-full sm:w-64 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tạm tính:</span>
                                <span><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Phí vận chuyển:</span>
                                <span>Miễn phí</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg pt-2 border-t">
                                <span>Tổng cộng:</span>
                                <span class="text-accent"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hủy đơn hàng -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold mb-4">Lý do hủy đơn hàng</h3>
        <input type="hidden" id="cancelOrderId">
        
        <div class="space-y-3 mb-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="cancel_reason" value="Tôi muốn thay đổi địa chỉ giao hàng" class="text-accent">
                <span>Tôi muốn thay đổi địa chỉ giao hàng</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="cancel_reason" value="Tôi muốn thay đổi sản phẩm (size, màu, số lượng)" class="text-accent">
                <span>Tôi muốn thay đổi sản phẩm (size, màu, số lượng)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="cancel_reason" value="Tôi tìm thấy giá rẻ hơn ở nơi khác" class="text-accent">
                <span>Tôi tìm thấy giá rẻ hơn ở nơi khác</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="cancel_reason" value="Tôi không còn nhu cầu mua nữa" class="text-accent">
                <span>Tôi không còn nhu cầu mua nữa</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="cancel_reason" value="Tôi đặt nhầm sản phẩm" class="text-accent">
                <span>Tôi đặt nhầm sản phẩm</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="cancel_reason" value="other" class="text-accent" id="otherReasonRadio">
                <span>Khác</span>
            </label>
        </div>
        
        <textarea id="otherReasonText" class="w-full border rounded-lg p-3 hidden" rows="3" 
                  placeholder="Nhập lý do của bạn..."></textarea>
        
        <div class="flex gap-3 mt-4">
            <button onclick="closeCancelModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                Đóng
            </button>
            <button onclick="submitCancel()" 
                    class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Xác nhận hủy
            </button>
        </div>
    </div>
</div>

<script>
function cancelOrder(orderId) {
    document.getElementById('cancelOrderId').value = orderId;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
    document.querySelectorAll('input[name="cancel_reason"]').forEach(r => r.checked = false);
    document.getElementById('otherReasonText').classList.add('hidden');
    document.getElementById('otherReasonText').value = '';
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
}

document.getElementById('otherReasonRadio').addEventListener('change', function() {
    document.getElementById('otherReasonText').classList.remove('hidden');
});
document.querySelectorAll('input[name="cancel_reason"]:not(#otherReasonRadio)').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('otherReasonText').classList.add('hidden');
    });
});

function submitCancel() {
    const orderId = document.getElementById('cancelOrderId').value;
    const selectedReason = document.querySelector('input[name="cancel_reason"]:checked');
    
    if (!selectedReason) {
        alert('Vui lòng chọn lý do hủy đơn');
        return;
    }
    
    let reason = selectedReason.value;
    if (reason === 'other') {
        reason = document.getElementById('otherReasonText').value.trim();
        if (!reason) {
            alert('Vui lòng nhập lý do hủy đơn');
            return;
        }
    }
    
    fetch('<?= BASE_URL ?>/profile/cancelOrder', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'order_id=' + orderId + '&cancel_reason=' + encodeURIComponent(reason)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '<?= BASE_URL ?>/profile/orders';
        } else {
            alert(data.message || 'Không thể hủy đơn hàng');
        }
    })
    .catch(() => alert('Có lỗi xảy ra'));
}

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
