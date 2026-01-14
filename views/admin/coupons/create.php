<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/admincoupon" class="p-2 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold">Thêm mã giảm giá</h1>
    </div>

    <?php $flash = Session::getFlash(); ?>
    <?php if ($flash && $flash['type'] === 'error'): ?>
    <div class="p-4 bg-red-100 text-red-700 rounded-lg"><?= $flash['message'] ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/admincoupon/create" method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Mã giảm giá *</label>
                <input type="text" name="code" required placeholder="VD: SALE20" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tên hiển thị</label>
                <input type="text" name="name" placeholder="VD: Giảm 20% cho đơn từ 1 triệu"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Mô tả</label>
            <textarea name="description" rows="2" placeholder="Mô tả chi tiết về mã giảm giá..."
                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent"></textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Loại giảm giá *</label>
                <select name="discount_type" id="discountType" onchange="toggleMaxDiscount()"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
                    <option value="percent">Phần trăm (%)</option>
                    <option value="fixed">Số tiền cố định</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Giá trị giảm *</label>
                <input type="number" name="discount_value" required min="0" step="0.01"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
            <div id="maxDiscountField">
                <label class="block text-sm font-medium mb-2">Giảm tối đa</label>
                <input type="number" name="max_discount" min="0" placeholder="VD: 100000"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Đơn hàng tối thiểu</label>
            <input type="number" name="min_order_value" min="0" value="0"
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Ngày kết thúc</label>
                <input type="datetime-local" name="end_date"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Giới hạn lượt dùng</label>
                <input type="number" name="usage_limit" min="1" value="100"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
            <div class="flex items-center pt-7">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="status" value="1" checked class="w-5 h-5 text-accent rounded">
                    <span>Kích hoạt ngay</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-save mr-2"></i>Lưu mã giảm giá
            </button>
            <a href="<?= BASE_URL ?>/admincoupon" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
function toggleMaxDiscount() {
    const type = document.getElementById('discountType').value;
    document.getElementById('maxDiscountField').style.display = type === 'percent' ? 'block' : 'none';
}
</script>
