<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-2xl font-bold">Báo cáo thống kê</h2>
        
        <!-- Bộ lọc thời gian -->
        <form method="GET" action="<?= BASE_URL ?>/adminreport" id="filterForm" class="flex flex-wrap items-center gap-3" onsubmit="return validateDateRange()">
            <label for="periodSelect" class="text-sm font-medium text-gray-600">
                <i class="fas fa-calendar-alt mr-1"></i>Thời gian:
            </label>
            <select name="period" id="periodSelect" onchange="toggleCustomDate(this.value)" 
                    title="Chọn khoảng thời gian"
                    class="px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-sm">
                <option value="today" <?= ($period ?? '') === 'today' ? 'selected' : '' ?>>Hôm nay</option>
                <option value="yesterday" <?= ($period ?? '') === 'yesterday' ? 'selected' : '' ?>>Hôm qua</option>
                <option value="7days" <?= ($period ?? '') === '7days' ? 'selected' : '' ?>>7 ngày qua</option>
                <option value="30days" <?= ($period ?? '') === '30days' ? 'selected' : '' ?>>30 ngày qua</option>
                <option value="this_month" <?= ($period ?? 'this_month') === 'this_month' ? 'selected' : '' ?>>Tháng này</option>
                <option value="last_month" <?= ($period ?? '') === 'last_month' ? 'selected' : '' ?>>Tháng trước</option>
                <option value="this_year" <?= ($period ?? '') === 'this_year' ? 'selected' : '' ?>>Năm nay</option>
                <option value="all" <?= ($period ?? '') === 'all' ? 'selected' : '' ?>>Tất cả</option>
                <option value="custom" <?= ($period ?? '') === 'custom' ? 'selected' : '' ?>>Tùy chọn</option>
            </select>
            <div id="customDateRange" class="flex items-center gap-2 <?= ($period ?? '') !== 'custom' ? 'hidden' : '' ?>">
                <label for="fromDate" class="text-sm text-gray-500">Từ:</label>
                <input type="date" name="from_date" id="fromDate" value="<?= $fromDate ?? '' ?>" 
                       title="Ngày bắt đầu" max="<?= date('Y-m-d') ?>"
                       class="px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-sm">
                <label for="toDate" class="text-sm text-gray-500">Đến:</label>
                <input type="date" name="to_date" id="toDate" value="<?= $toDate ?? '' ?>" 
                       title="Ngày kết thúc" max="<?= date('Y-m-d') ?>"
                       class="px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-sm">
            </div>
            <button type="submit" title="Áp dụng bộ lọc" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm">
                <i class="fas fa-filter mr-1"></i> Lọc
            </button>
            <?php if (($period ?? 'this_month') !== 'this_month'): ?>
            <a href="<?= BASE_URL ?>/adminreport" title="Xóa bộ lọc" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm">
                <i class="fas fa-times mr-1"></i> Xóa lọc
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Hiển thị khoảng thời gian đang xem -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 text-sm text-blue-700">
        <i class="fas fa-info-circle mr-2"></i>
        Đang xem báo cáo: <strong><?= $periodLabel ?? 'Tháng này' ?></strong>
        <?php if (!empty($fromDate) && !empty($toDate)): ?>
            (<?= date('d/m/Y', strtotime($fromDate)) ?> - <?= date('d/m/Y', strtotime($toDate)) ?>)
        <?php endif; ?>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Doanh thu theo bộ lọc -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Doanh thu <?= strtolower($periodLabel ?? '') ?></p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($filteredRevenue['revenue'] ?? 0, 0, ',', '.') ?>đ</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2"><?= $filteredRevenue['orders'] ?? 0 ?> đơn hoàn thành</p>
        </div>

        <!-- Tổng đơn hàng theo bộ lọc -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Đơn hàng <?= strtolower($periodLabel ?? '') ?></p>
                    <p class="text-2xl font-bold text-blue-600"><?= $filteredOrders['total'] ?? 0 ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2"><?= $filteredOrders['completed'] ?? 0 ?> hoàn thành</p>
        </div>

        <!-- Doanh thu hôm nay -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Doanh thu hôm nay</p>
                    <p class="text-2xl font-bold text-purple-600"><?= number_format($todayRevenue['revenue'] ?? 0, 0, ',', '.') ?>đ</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-day text-purple-600"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2"><?= $todayRevenue['orders'] ?? 0 ?> đơn hàng</p>
        </div>

        <!-- Chờ xử lý -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Chờ xử lý <?= strtolower($periodLabel ?? '') ?></p>
                    <p class="text-2xl font-bold text-orange-600"><?= $filteredOrders['pending'] ?? 0 ?></p>
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
            <div class="h-80 flex items-center justify-center">
                <?php if (empty($revenueByMonth)): ?>
                <p class="text-gray-400">Chưa có dữ liệu doanh thu</p>
                <?php else: ?>
                <canvas id="revenueChart" class="w-full"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- Biểu đồ trạng thái đơn hàng -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                Trạng thái đơn hàng <span class="text-sm font-normal text-gray-400">(<?= $periodLabel ?? '' ?>)</span>
            </h3>
            <div class="h-80 flex items-center justify-center">
                <?php if (empty($orderStats)): ?>
                <p class="text-gray-400">Không có dữ liệu trong khoảng thời gian này</p>
                <?php else: ?>
                <canvas id="orderStatusChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Products with Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Biểu đồ top sản phẩm -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                Top sản phẩm bán chạy <span class="text-sm font-normal text-gray-400">(<?= $periodLabel ?? '' ?>)</span>
            </h3>
            <div class="h-80 flex items-center justify-center">
                <?php if (empty($topProducts)): ?>
                <p class="text-gray-400">Không có dữ liệu trong khoảng thời gian này</p>
                <?php else: ?>
                <canvas id="topProductsChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bảng top sản phẩm -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-bold mb-4">
                <i class="fas fa-list text-purple-500 mr-2"></i>
                Chi tiết top 10 sản phẩm <span class="text-sm font-normal text-gray-400">(<?= $periodLabel ?? '' ?>)</span>
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
// Hàm xử lý bộ lọc
function toggleCustomDate(value) {
    const customDiv = document.getElementById('customDateRange');
    if (value === 'custom') {
        customDiv.classList.remove('hidden');
        const fromDate = document.getElementById('fromDate');
        const toDate = document.getElementById('toDate');
        if (!fromDate.value) {
            const d = new Date();
            d.setDate(d.getDate() - 7);
            fromDate.value = d.toISOString().split('T')[0];
        }
        if (!toDate.value) {
            toDate.value = new Date().toISOString().split('T')[0];
        }
    } else {
        customDiv.classList.add('hidden');
    }
}

function validateDateRange() {
    const period = document.getElementById('periodSelect').value;
    if (period === 'custom') {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        if (!fromDate || !toDate) {
            alert('Vui lòng chọn ngày bắt đầu và ngày kết thúc');
            return false;
        }
        if (fromDate > toDate) {
            alert('Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc');
            return false;
        }
    }
    return true;
}
// Dữ liệu từ PHP
const revenueData = <?= json_encode($revenueByMonth) ?>;
const orderStatsData = <?= json_encode($orderStats) ?>;
const topProductsData = <?= json_encode($topProducts) ?>;

// 1. Biểu đồ doanh thu theo tháng (Bar Chart)
<?php if (!empty($revenueByMonth)): ?>
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
<?php endif; ?>

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

<?php if (!empty($orderStats)): ?>
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
<?php endif; ?>

// 3. Biểu đồ top sản phẩm (Horizontal Bar Chart)
<?php if (!empty($topProducts)): ?>
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
<?php endif; ?>
</script>
