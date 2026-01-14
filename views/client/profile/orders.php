<?php
/**
 * Trang danh sách đơn hàng - Gộp tất cả đơn hàng với tabs lọc trạng thái
 */

// Hàm hiển thị trạng thái đơn hàng
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

$currentStatus = $_GET['status'] ?? 'all';
$statusTabs = [
    'all' => 'Tất cả',
    'pending' => 'Chờ xác nhận',
    'processing' => 'Đang xử lý', 
    'shipping' => 'Đang giao',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy',
];
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/views/client/profile/_sidebar.php'; ?>

        <!-- Content -->
        <div class="flex-1">
            <div class="bg-white rounded-xl shadow-sm">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold">Đơn hàng của tôi</h2>
                </div>

                <!-- Status Tabs -->
                <div class="border-b overflow-x-auto">
                    <div class="flex min-w-max">
                        <?php foreach ($statusTabs as $status => $label): ?>
                        <a href="<?= BASE_URL ?>/profile/orders<?= $status !== 'all' ? '?status=' . $status : '' ?>" 
                           class="px-6 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap <?= $currentStatus === $status ? 'border-accent text-accent' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                            <?= $label ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="p-6">
                <?php if (empty($orders)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">Không có đơn hàng nào</p>
                    <a href="<?= BASE_URL ?>/home/products" class="inline-block px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600">
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
                                <?php foreach (array_slice($order['items'], 0, 4) as $item): ?>
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
                                <span class="font-bold <?= $order['status'] === 'cancelled' ? 'text-gray-400 line-through' : 'text-accent' ?> text-base">
                                    <?= number_format($order['total_money'], 0, ',', '.') ?>đ
                                </span>
                                <span class="text-gray-400 mx-2">|</span>
                                <?= $order['item_count'] ?> sản phẩm
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if ($order['status'] === 'pending'): ?>
                                <button onclick="cancelOrder(<?= $order['id'] ?>)" 
                                        class="px-4 py-1.5 text-sm border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition">
                                    Hủy đơn
                                </button>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'completed'): ?>
                                <button onclick="reorder(<?= $order['id'] ?>)" 
                                        class="px-4 py-1.5 text-sm bg-accent text-white rounded-lg hover:bg-red-600 transition">
                                    Mua lại
                                </button>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>/profile/orderDetail/<?= $order['id'] ?>" 
                                   class="px-4 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                                    Xem chi tiết
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
                <input type="radio" name="cancel_reason" value="Tôi muốn thay đổi sản phẩm" class="text-accent">
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

document.getElementById('otherReasonRadio')?.addEventListener('change', function() {
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
            location.reload();
        } else {
            alert(data.message || 'Không thể hủy đơn hàng');
        }
    })
    .catch(() => alert('Có lỗi xảy ra'));
}

function reorder(orderId) {
    if (confirm('Thêm các sản phẩm trong đơn hàng này vào giỏ hàng?')) {
        window.location.href = '<?= BASE_URL ?>/profile/reorder/' + orderId;
    }
}

document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
