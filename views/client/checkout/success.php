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
                <span><?= $order['payment_method'] === 'COD' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng' ?></span>
            </div>
        </div>

        <?php if ($order['payment_method'] === 'Banking'): ?>
        <!-- Thông tin chuyển khoản -->
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 text-left mb-6 border border-blue-200">
            <h3 class="font-bold text-lg text-blue-800 mb-4 flex items-center gap-2">
                <i class="fas fa-university"></i>
                Thông tin chuyển khoản
            </h3>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center bg-white rounded-lg p-3">
                    <span class="text-gray-600">Ngân hàng</span>
                    <span class="font-bold">Vietcombank</span>
                </div>
                <div class="flex justify-between items-center bg-white rounded-lg p-3">
                    <span class="text-gray-600">Số tài khoản</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold font-mono" id="bankAccount">1234567890123</span>
                        <button onclick="copyToClipboard('1234567890123')" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center bg-white rounded-lg p-3">
                    <span class="text-gray-600">Chủ tài khoản</span>
                    <span class="font-bold">NGUYEN VAN A</span>
                </div>
                <div class="flex justify-between items-center bg-white rounded-lg p-3">
                    <span class="text-gray-600">Số tiền</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-accent"><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                        <button onclick="copyToClipboard('<?= $order['total_money'] ?>')" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center bg-white rounded-lg p-3">
                    <span class="text-gray-600">Nội dung CK</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-blue-600 font-mono">DH<?= $order['id'] ?></span>
                        <button onclick="copyToClipboard('DH<?= $order['id'] ?>')" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Lưu ý:</strong> Vui lòng chuyển khoản đúng số tiền và nội dung để đơn hàng được xử lý nhanh chóng.
                </p>
            </div>

            <!-- QR Code VietQR -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600 mb-2">Quét mã QR để chuyển khoản nhanh</p>
                <img src="https://img.vietqr.io/image/VCB-1234567890123-compact2.png?amount=<?= $order['total_money'] ?>&addInfo=DH<?= $order['id'] ?>&accountName=NGUYEN%20VAN%20A" 
                     alt="QR Code" class="w-48 h-48 mx-auto rounded-lg border">
            </div>
        </div>
        <?php endif; ?>

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

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Hiển thị thông báo
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.innerHTML = '<i class="fas fa-check mr-2"></i>Đã sao chép!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>
