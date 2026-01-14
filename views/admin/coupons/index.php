<?php $canEdit = Session::isAdmin(); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Quản lý mã giảm giá</h1>
        <?php if ($canEdit): ?>
        <a href="<?= BASE_URL ?>/admincoupon/create" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-red-600 transition">
            <i class="fas fa-plus mr-2"></i>Thêm mã mới
        </a>
        <?php else: ?>
        <span class="text-sm text-gray-500 italic">Chỉ Admin mới có quyền quản lý mã giảm giá</span>
        <?php endif; ?>
    </div>

    <?php $flash = Session::getFlash(); ?>
    <?php if ($flash && $flash['type'] === 'success'): ?>
    <div class="p-4 bg-green-100 text-green-700 rounded-lg"><?= $flash['message'] ?></div>
    <?php elseif ($flash && $flash['type'] === 'error'): ?>
    <div class="p-4 bg-red-100 text-red-700 rounded-lg"><?= $flash['message'] ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium">Mã</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Tên</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Giảm giá</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Đơn tối thiểu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Thời hạn</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Đã dùng</th>
                    <th class="px-4 py-3 text-left text-sm font-medium">Trạng thái</th>
                    <th class="px-4 py-3 text-center text-sm font-medium">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($coupons)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">Chưa có mã giảm giá nào</td>
                </tr>
                <?php else: ?>
                <?php foreach ($coupons as $coupon): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-accent/10 text-accent font-mono font-bold rounded"><?= $coupon['code'] ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium"><?= htmlspecialchars($coupon['name'] ?? $coupon['code']) ?></p>
                        <?php if ($coupon['description']): ?>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($coupon['description']) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($coupon['discount_type'] === 'percent'): ?>
                        <span class="text-green-600 font-medium"><?= (int)$coupon['discount_value'] ?>%</span>
                        <?php if ($coupon['max_discount']): ?>
                        <span class="text-xs text-gray-500">(tối đa <?= formatMoney($coupon['max_discount']) ?>)</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-green-600 font-medium"><?= formatMoney($coupon['discount_value']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3"><?= formatMoney($coupon['min_order_value']) ?></td>
                    <td class="px-4 py-3 text-sm">
                        <?php if ($coupon['start_date'] && $coupon['end_date']): ?>
                        <p><?= date('d/m/Y', strtotime($coupon['start_date'])) ?></p>
                        <p class="text-gray-500">→ <?= date('d/m/Y', strtotime($coupon['end_date'])) ?></p>
                        <?php else: ?>
                        <span class="text-gray-400">Không giới hạn</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium"><?= $coupon['used_count'] ?></span>
                        <span class="text-gray-500">/ <?= $coupon['usage_limit'] ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <?php 
                        $isExpired = $coupon['end_date'] && strtotime($coupon['end_date']) < time();
                        $isActive = $coupon['status'] && !$isExpired && $coupon['used_count'] < $coupon['usage_limit'];
                        ?>
                        <?php if ($isActive): ?>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Hoạt động</span>
                        <?php elseif ($isExpired): ?>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Hết hạn</span>
                        <?php elseif ($coupon['used_count'] >= $coupon['usage_limit']): ?>
                        <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs rounded-full">Hết lượt</span>
                        <?php else: ?>
                        <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full">Tắt</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($canEdit): ?>
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= BASE_URL ?>/admincoupon/edit/<?= $coupon['id'] ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admincoupon/toggle/<?= $coupon['id'] ?>" 
                               class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg" title="Bật/Tắt">
                                <i class="fas fa-power-off"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admincoupon/delete/<?= $coupon['id'] ?>" 
                               onclick="return confirm('Xóa mã giảm giá này?')"
                               class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <?php else: ?>
                        <span class="text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
