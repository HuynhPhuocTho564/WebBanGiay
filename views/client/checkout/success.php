<div class="container mx-auto px-4 py-12">
    <div class="max-w-lg mx-auto text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-4xl text-green-500"></i>
        </div>
        
        <h1 class="text-2xl font-bold mb-2">Đặt hàng thành công!</h1>
        <p class="text-gray-500 mb-6">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.</p>

        <div class="bg-white rounded-xl shadow-sm p-6 text-left mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500">Mã đơn hàng</span>
                <span class="font-bold text-lg">#<?= $order['id'] ?></span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500">Ngày đặt</span>
                <span><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-500">Tổng tiền</span>
                <span class="font-bold text-accent"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-500">Thanh toán</span>
                <span><?= $order['payment_method'] === 'COD' ? 'Thanh toán khi nhận hàng' : $order['payment_method'] ?></span>
            </div>
        </div>

        <div class="flex gap-4 justify-center">
            <a href="<?= BASE_URL ?>/profile/orderDetail/<?= $order['id'] ?>" 
               class="px-6 py-3 bg-accent text-white rounded-lg hover:bg-red-600 transition">
                Xem chi tiết đơn hàng
            </a>
            <a href="<?= BASE_URL ?>" class="px-6 py-3 border rounded-lg hover:bg-gray-50 transition">
                Về trang chủ
            </a>
        </div>
    </div>
</div>
