<div class="space-y-6">
    <h2 class="text-2xl font-bold">Báo cáo thống kê</h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Doanh thu hôm nay</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($todayRevenue['revenue'] ?? 0, 0, ',', '.') ?>đ</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2"><?= $todayRevenue['orders'] ?? 0 ?> đơn hàng</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Doanh thu tháng này</p>
                    <p class="text-2xl font-bold text-blue-600"><?= number_format($monthRevenue['revenue'] ?? 0, 0, ',', '.') ?>đ</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2"><?= $monthRevenue['orders'] ?? 0 ?> đơn hàng</p>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="font-bold mb-4">Top 10 sản phẩm bán chạy</h3>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Sản phẩm</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Đã bán</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Doanh thu</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php $i = 1; foreach ($topProducts as $p): ?>
                <tr>
                    <td class="px-4 py-3"><?= $i++ ?></td>
                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($p['name']) ?></td>
                    <td class="px-4 py-3"><?= $p['total_sold'] ?></td>
                    <td class="px-4 py-3 text-accent"><?= number_format($p['revenue'], 0, ',', '.') ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
