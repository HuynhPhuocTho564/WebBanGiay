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

        <?php 
        $totalOrders = array_sum(array_column($orderStats, 'count'));
        $completedOrders = 0;
        $pendingOrders = 0;
        foreach ($orderStats as $stat) {
            if ($stat['status'] === 'completed') $completedOrders = $stat['count'];
            if ($stat['status'] === 'pending') $pendingOrders = $stat['count'];
        }
        ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng đơn hàng</p>
                    <p class="text-2xl font-bold text-purple-600"><?= $totalOrders ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-purple-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2"><?= $completedOrders ?> hoàn thành</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Chờ xử lý</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $pendingOrders ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">đơn hàng</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Biểu đồ doanh thu theo tháng -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                Doanh thu 12 tháng gần nhất
            </h3>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Biểu đồ trạng thái đơn hàng -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                Trạng thái đơn hàng
            </h3>
            <div class="h-80">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products with Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Biểu đồ top sản phẩm -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                Top sản phẩm bán chạy
            </h3>
            <div class="h-80">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>

        <!-- Bảng top sản phẩm -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-list text-purple-500 mr-2"></i>
                Chi tiết top 10 sản phẩm
            </h3>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">#</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Sản phẩm</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-600">Đã bán</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php $i = 1; foreach ($topProducts as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm">
                                <?php if ($i <= 3): ?>
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full 
                                        <?= $i == 1 ? 'bg-yellow-100 text-yellow-600' : ($i == 2 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') ?>">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <?= $i ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-sm font-medium"><?= htmlspecialchars($p['name']) ?></td>
                            <td class="px-3 py-2 text-sm text-center"><?= $p['total_sold'] ?></td>
                            <td class="px-3 py-2 text-sm text-right text-green-600 font-medium"><?= number_format($p['revenue'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php $i++; endforeach; ?>
                        <?php if (empty($topProducts)): ?>
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-gray-500">Chưa có dữ liệu</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dữ liệu từ PHP
const revenueData = <?= json_encode($revenueByMonth) ?>;
const orderStatsData = <?= json_encode($orderStats) ?>;
const topProductsData = <?= json_encode($topProducts) ?>;

// 1. Biểu đồ doanh thu theo tháng (Bar Chart)
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: revenueData.map(item => {
            const [year, month] = item.month.split('-');
            return `T${month}/${year.slice(2)}`;
        }),
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: revenueData.map(item => item.revenue),
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return new Intl.NumberFormat('vi-VN').format(context.raw) + 'đ';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) return (value / 1000000) + 'M';
                        if (value >= 1000) return (value / 1000) + 'K';
                        return value;
                    }
                }
            }
        }
    }
});

// 2. Biểu đồ trạng thái đơn hàng (Doughnut Chart)
const statusLabels = {
    'pending': 'Chờ xác nhận',
    'processing': 'Đang xử lý', 
    'shipping': 'Đang giao',
    'completed': 'Hoàn thành',
    'cancelled': 'Đã hủy',
    'returning': 'Yêu cầu đổi trả',
    'returned': 'Đã đổi trả'
};
const statusColors = {
    'pending': '#F59E0B',
    'processing': '#3B82F6',
    'shipping': '#8B5CF6',
    'completed': '#10B981',
    'cancelled': '#EF4444',
    'returning': '#F97316',
    'returned': '#6B7280'
};

const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: orderStatsData.map(item => statusLabels[item.status] || item.status),
        datasets: [{
            data: orderStatsData.map(item => item.count),
            backgroundColor: orderStatsData.map(item => statusColors[item.status] || '#6B7280'),
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: { padding: 15, usePointStyle: true }
            }
        }
    }
});

// 3. Biểu đồ top sản phẩm (Horizontal Bar Chart)
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
new Chart(topProductsCtx, {
    type: 'bar',
    data: {
        labels: topProductsData.slice(0, 5).map(item => {
            const name = item.name;
            return name.length > 20 ? name.substring(0, 20) + '...' : name;
        }),
        datasets: [{
            label: 'Số lượng bán',
            data: topProductsData.slice(0, 5).map(item => item.total_sold),
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(156, 163, 175, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)'
            ],
            borderColor: [
                'rgba(255, 193, 7, 1)',
                'rgba(156, 163, 175, 1)',
                'rgba(249, 115, 22, 1)',
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)'
            ],
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
