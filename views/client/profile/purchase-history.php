<?php
/**
 * Trang lịch sử mua hàng (đã hoàn thành/hủy)
 */

if (!function_exists('getOrderStatusBadge')) {
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
    return '<span class="px-2 py-1 text-xs font-medium rounded-full ' . $badge[0] . '">' . $badge[1] . '</span>';
}
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/views/client/profile/_sidebar.php'; ?>

        <!-- Content -->
        <div class="flex-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold mb-6">Lịch sử mua hàng</h2>

                <?php if (empty($orders)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-history text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">Bạn chưa có lịch sử mua hàng</p>
                    <a href="<?= BASE_URL ?>/products" class="inline-block px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600">
                        Mua sắm ngay
                    </a>
                </div>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($orders as $order): ?>
                    <div class="border rounded-lg p-4 hover:shadow-md transition <?= $order['status'] === 'cancelled' ? 'bg-gray-50' : '' ?>">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                            <div>
                                <span class="font-medium">Đơn hàng #<?= $order['id'] ?></span>
                                <span class="text-sm text-gray-500 ml-2">
                                    <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?>
                                </span>
                            </div>
                            <?= getOrderStatusBadge($order['status']) ?>
                        </div>

                        <!-- Sản phẩm trong đơn -->
                        <?php if (!empty($order['items'])): ?>
                        <div class="flex items-center gap-3 py-3 border-t border-b">
                            <div class="flex -space-x-2">
                                <?php foreach ($order['items'] as $item): ?>
                                <img src="<?= productImage($item['thumbnail']) ?>" alt="<?= $item['name'] ?>" 
                                     class="w-12 h-12 rounded-lg object-cover border-2 border-white" title="<?= $item['name'] ?>">
                                <?php endforeach; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate"><?= $order['items'][0]['name'] ?></p>
                                <p class="text-xs text-gray-500">
                                    <?= $order['items'][0]['color'] ?> / Size <?= $order['items'][0]['size'] ?>
                                    <?php if ($order['item_count'] > 1): ?>
                                    <span class="text-accent">+<?= $order['item_count'] - 1 ?> sản phẩm khác</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= htmlspecialchars(mb_substr($order['address'], 0, 50)) ?>...
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold <?= $order['status'] === 'cancelled' ? 'text-gray-400 line-through' : 'text-accent' ?>">
                                    <?= number_format($order['total_money'], 0, ',', '.') ?>đ
                                </span>
                                <?php if ($order['status'] === 'completed'): ?>
                                <a href="<?= BASE_URL ?>/profile/orderDetail/<?= $order['id'] ?>" 
                                   class="px-4 py-1.5 text-sm bg-accent text-white rounded-lg hover:bg-red-600 transition">
                                    Mua lại
                                </a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>/profile/orderDetail/<?= $order['id'] ?>" 
                                   class="px-4 py-1.5 text-sm border border-accent text-accent rounded-lg hover:bg-accent hover:text-white transition">
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
