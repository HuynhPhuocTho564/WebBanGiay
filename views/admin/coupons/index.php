<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold">Quản lý mã giảm giá</h2>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Thêm mã
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Mã</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Giảm giá</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Đơn tối thiểu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Thời gian</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($coupons)): ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Chưa có mã giảm giá</td></tr>
                <?php else: foreach ($coupons as $c): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold"><?= $c['code'] ?></td>
                    <td class="px-4 py-3"><?= $c['discount_type'] === 'percent' ? $c['discount_value'].'%' : number_format($c['discount_value'],0,',','.').'đ' ?></td>
                    <td class="px-4 py-3"><?= number_format($c['min_order_value'],0,',','.') ?>đ</td>
                    <td class="px-4 py-3 text-sm"><?= date('d/m/Y', strtotime($c['start_date'])) ?> - <?= date('d/m/Y', strtotime($c['end_date'])) ?></td>
                    <td class="px-4 py-3"><span class="px-2 py-1 text-xs rounded-full <?= $c['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>"><?= $c['status'] ? 'Hoạt động' : 'Tắt' ?></span></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?= BASE_URL ?>/admincoupon/delete/<?= $c['id'] ?>" onclick="return confirm('Xác nhận xóa?')" class="text-red-500 hover:underline">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Thêm mã giảm giá</h3>
        <form action="<?= BASE_URL ?>/admincoupon/store" method="POST" class="space-y-4">
            <input type="text" name="code" placeholder="Mã giảm giá" required class="w-full px-4 py-2 border rounded-lg">
            <select name="discount_type" class="w-full px-4 py-2 border rounded-lg">
                <option value="percent">Phần trăm (%)</option>
                <option value="fixed">Số tiền cố định</option>
            </select>
            <input type="number" name="discount_value" placeholder="Giá trị giảm" required class="w-full px-4 py-2 border rounded-lg">
            <input type="number" name="min_order_value" placeholder="Đơn tối thiểu" class="w-full px-4 py-2 border rounded-lg">
            <input type="datetime-local" name="start_date" required class="w-full px-4 py-2 border rounded-lg">
            <input type="datetime-local" name="end_date" required class="w-full px-4 py-2 border rounded-lg">
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 px-4 py-2 border rounded-lg">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-accent text-white rounded-lg">Thêm</button>
            </div>
        </form>
    </div>
</div>
