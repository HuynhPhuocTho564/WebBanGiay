<?php
function getStatusBadge($status) {
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

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/adminorder" class="text-gray-500 hover:text-accent">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-2xl font-bold">Chi tiết đơn hàng #<?= $order['id'] ?></h2>
        <?= getStatusBadge($order['status']) ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-4">Thông tin khách hàng</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Họ tên:</span>
                        <p class="font-medium"><?= htmlspecialchars($order['fullname']) ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500">Số điện thoại:</span>
                        <p class="font-medium"><?= $order['phone_number'] ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500">Email:</span>
                        <p class="font-medium"><?= $order['email'] ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500">Thanh toán:</span>
                        <p class="font-medium"><?= $order['payment_method'] ?></p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500">Địa chỉ:</span>
                        <p class="font-medium"><?= htmlspecialchars($order['address']) ?></p>
                    </div>
                    <?php if (!empty($order['note'])): ?>
                    <div class="col-span-2">
                        <span class="text-gray-500">Ghi chú:</span>
                        <p class="font-medium"><?= htmlspecialchars($order['note']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-4">Sản phẩm đã đặt</h3>
                <div class="space-y-4">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="flex gap-4 p-3 bg-gray-50 rounded-lg">
                        <?php 
                        $imgSrc = $item['thumbnail'];
                        if (!filter_var($imgSrc, FILTER_VALIDATE_URL)) {
                            $imgSrc = ASSETS_URL . '/images/products/' . $imgSrc;
                        }
                        ?>
                        <img src="<?= $imgSrc ?>" alt="" class="w-16 h-16 object-cover rounded">
                        <div class="flex-1">
                            <p class="font-medium"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="text-sm text-gray-500">Size: <?= $item['size'] ?> | Màu: <?= $item['color'] ?></p>
                            <div class="flex justify-between mt-1">
                                <span class="text-sm">x<?= $item['quantity'] ?></span>
                                <span class="font-medium text-accent"><?= number_format($item['total_item_price'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="border-t mt-4 pt-4 text-right">
                    <span class="text-lg font-bold">Tổng cộng: 
                        <span class="text-accent"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-4">Cập nhật trạng thái</h3>
                <form action="<?= BASE_URL ?>/adminorder/updateStatus" method="POST">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status" class="w-full px-4 py-2 border rounded-lg mb-4">
                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="shipping" <?= $order['status'] === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                    <button type="submit" class="w-full px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
                        Cập nhật
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-4">Thông tin đơn hàng</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mã đơn hàng:</span>
                        <span class="font-medium">#<?= $order['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ngày đặt:</span>
                        <span><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Thanh toán:</span>
                        <span><?= $order['payment_method'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
