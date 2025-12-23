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
    return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $badge[0] . '">' . $badge[1] . '</span>';
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold">Quản lý đơn hàng</h2>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" <?= ($currentStatus ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                <option value="processing" <?= ($currentStatus ?? '') === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                <option value="shipping" <?= ($currentStatus ?? '') === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                <option value="completed" <?= ($currentStatus ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                <option value="cancelled" <?= ($currentStatus ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
            </select>
            <input type="text" name="q" placeholder="Tìm theo mã, tên, SĐT..." 
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   class="px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
                <i class="fas fa-search mr-2"></i>Lọc
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Mã ĐH</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Khách hàng</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">SĐT</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tổng tiền</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Ngày đặt</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Không có đơn hàng nào</td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">#<?= $order['id'] ?></td>
                    <td class="px-4 py-3"><?= htmlspecialchars($order['fullname']) ?></td>
                    <td class="px-4 py-3"><?= $order['phone_number'] ?></td>
                    <td class="px-4 py-3 font-medium text-accent"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</td>
                    <td class="px-4 py-3"><?= getStatusBadge($order['status']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?= BASE_URL ?>/adminorder/detail/<?= $order['id'] ?>" 
                           class="text-accent hover:underline">Chi tiết</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
