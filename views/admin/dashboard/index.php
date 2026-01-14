<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 mb-6 text-white">
    <div>
        <h1 class="text-2xl font-bold mb-2">Xin chào, <?= Session::user()['fullname'] ?>! 👋</h1>
        <p class="text-blue-100">Đây là tổng quan cửa hàng của bạn</p>
    </div>
</div>

<!-- Bộ lọc thời gian -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="<?= BASE_URL ?>/admin" id="filterForm" class="flex flex-wrap items-center gap-3" onsubmit="return validateDateRange()">
        <label for="periodSelect" class="text-sm font-medium text-gray-600">
            <i class="fas fa-calendar-alt mr-2"></i>Thời gian:
        </label>
        <span class="text-xs text-blue-500 ml-2 font-medium"><?= $periodLabel ?? 'Hôm nay' ?></span>
        <select name="period" id="periodSelect" onchange="toggleCustomDate(this.value)" 
                title="Chọn khoảng thời gian"
                class="px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-sm">
            <option value="today" <?= ($period ?? 'today') === 'today' ? 'selected' : '' ?>>Hôm nay</option>
            <option value="yesterday" <?= ($period ?? '') === 'yesterday' ? 'selected' : '' ?>>Hôm qua</option>
            <option value="7days" <?= ($period ?? '') === '7days' ? 'selected' : '' ?>>7 ngày qua</option>
            <option value="30days" <?= ($period ?? '') === '30days' ? 'selected' : '' ?>>30 ngày qua</option>
            <option value="this_month" <?= ($period ?? '') === 'this_month' ? 'selected' : '' ?>>Tháng này</option>
            <option value="last_month" <?= ($period ?? '') === 'last_month' ? 'selected' : '' ?>>Tháng trước</option>
            <option value="this_year" <?= ($period ?? '') === 'this_year' ? 'selected' : '' ?>>Năm nay</option>
            <option value="custom" <?= ($period ?? '') === 'custom' ? 'selected' : '' ?>>Tùy chọn</option>
        </select>
        <div id="customDateRange" class="flex items-center gap-2 <?= ($period ?? '') !== 'custom' ? 'hidden' : '' ?>">
            <label for="fromDate" class="text-sm text-gray-500">Từ:</label>
            <input type="date" name="from_date" id="fromDate" value="<?= $fromDate ?? '' ?>" 
                   title="Ngày bắt đầu" max="<?= date('Y-m-d') ?>"
                   onchange="updateToDateMin()"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-sm">
            <label for="toDate" class="text-sm text-gray-500">Đến:</label>
            <input type="date" name="to_date" id="toDate" value="<?= $toDate ?? '' ?>" 
                   title="Ngày kết thúc" max="<?= date('Y-m-d') ?>"
                   class="px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-sm">
        </div>
        <button type="submit" title="Áp dụng bộ lọc" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm">
            <i class="fas fa-filter mr-1"></i> Lọc
        </button>
        <?php if (($period ?? 'today') !== 'today'): ?>
        <a href="<?= BASE_URL ?>/admin" title="Xóa bộ lọc" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm">
            <i class="fas fa-times mr-1"></i> Xóa lọc
        </a>
        <?php endif; ?>
    </form>
</div>

<script>
function toggleCustomDate(value) {
    const customDiv = document.getElementById('customDateRange');
    if (value === 'custom') {
        customDiv.classList.remove('hidden');
        // Set default dates if empty
        const fromDate = document.getElementById('fromDate');
        const toDate = document.getElementById('toDate');
        if (!fromDate.value) {
            // Default: 7 ngày trước
            const d = new Date();
            d.setDate(d.getDate() - 7);
            fromDate.value = d.toISOString().split('T')[0];
        }
        if (!toDate.value) {
            toDate.value = new Date().toISOString().split('T')[0];
        }
        updateToDateMin();
    } else {
        customDiv.classList.add('hidden');
    }
}

function updateToDateMin() {
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    if (fromDate.value) {
        toDate.min = fromDate.value;
        // Nếu toDate < fromDate thì set toDate = fromDate
        if (toDate.value && toDate.value < fromDate.value) {
            toDate.value = fromDate.value;
        }
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
</script>

<!-- Stats Cards Row 1 -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php if (Session::isAdmin()): ?>
    <!-- Doanh thu theo khoảng thời gian - Chỉ Admin -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-500">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Doanh thu <?= strtolower($periodLabel ?? 'hôm nay') ?></p>
                <p class="text-xl font-bold text-gray-800"><?= formatMoney($stats['filteredRevenue'] ?? 0) ?></p>
                <p class="text-xs text-green-600 mt-2">
                    <i class="fas fa-calendar-alt"></i> <?= $periodLabel ?? 'Hôm nay' ?>
                </p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet text-green-600"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Đơn hàng theo khoảng thời gian -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-500">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Đơn hàng <?= strtolower($periodLabel ?? 'hôm nay') ?></p>
                <p class="text-xl font-bold text-gray-800"><?= number_format($stats['filteredOrders'] ?? 0) ?></p>
                <p class="text-xs text-blue-600 mt-2">
                    <i class="fas fa-shopping-bag"></i> <?= $periodLabel ?? 'Hôm nay' ?>
                </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Chờ xử lý -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-orange-500">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Chờ xử lý</p>
                <p class="text-xl font-bold text-gray-800"><?= number_format($stats['pendingOrders']) ?></p>
                <a href="<?= BASE_URL ?>/adminorder?status=pending" class="text-xs text-orange-600 mt-2 inline-block hover:underline">
                    <i class="fas fa-clock"></i> Xử lý ngay
                </a>
            </div>
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-orange-600"></i>
            </div>
        </div>
    </div>

    <!-- Đang giao -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-500">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Đang giao hàng</p>
                <p class="text-xl font-bold text-gray-800"><?= number_format($stats['shippingOrders']) ?></p>
                <p class="text-xs text-purple-600 mt-2">
                    <i class="fas fa-truck"></i> Đơn đang ship
                </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-truck text-purple-600"></i>
            </div>
        </div>
    </div>

    <?php if (!Session::isAdmin()): ?>
    <!-- Sản phẩm - Hiện cho nhân viên thay doanh thu -->
    <div class="bg-white rounded-xl p-5 shadow-sm border-l-4 border-violet-500">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Tổng sản phẩm</p>
                <p class="text-xl font-bold text-gray-800"><?= number_format($stats['totalProducts']) ?></p>
                <p class="text-xs text-violet-600 mt-2">
                    <i class="fas fa-box"></i> Trong kho
                </p>
            </div>
            <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-box text-violet-600"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (Session::isAdmin()): ?>
<!-- Stats Cards Row 2 - Chỉ Admin -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Tổng doanh thu -->
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-emerald-600"></i>
            </div>
            <span class="text-sm text-gray-500">Tổng doanh thu</span>
        </div>
        <p class="text-2xl font-bold text-emerald-600"><?= formatMoney($stats['totalRevenue']) ?></p>
    </div>

    <!-- Tổng đơn hàng -->
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-receipt text-blue-600"></i>
            </div>
            <span class="text-sm text-gray-500">Tổng đơn hàng</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalOrders']) ?></p>
    </div>

    <!-- Sản phẩm -->
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-box text-violet-600"></i>
            </div>
            <span class="text-sm text-gray-500">Sản phẩm</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalProducts']) ?></p>
    </div>

    <!-- Khách hàng -->
    <div class="bg-white rounded-xl p-5 shadow-sm">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-pink-600"></i>
            </div>
            <span class="text-sm text-gray-500">Khách hàng</span>
        </div>
        <p class="text-2xl font-bold text-gray-800"><?= number_format($stats['totalUsers']) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Order Status Overview -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6">
    <h2 class="font-bold mb-4">Tình trạng đơn hàng <span class="text-sm font-normal text-gray-400">(Tổng tất cả)</span></h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="<?= BASE_URL ?>/adminorder?status=pending" class="text-center p-4 rounded-xl bg-yellow-50 hover:bg-yellow-100 transition">
            <div class="w-12 h-12 bg-yellow-200 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-yellow-600"><?= $stats['pendingOrders'] ?></p>
            <p class="text-xs text-gray-500">Chờ xác nhận</p>
        </a>
        <a href="<?= BASE_URL ?>/adminorder?status=processing" class="text-center p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition">
            <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-cog text-blue-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-blue-600"><?= $stats['processingOrders'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Đang xử lý</p>
        </a>
        <a href="<?= BASE_URL ?>/adminorder?status=shipping" class="text-center p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition">
            <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-truck text-purple-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-purple-600"><?= $stats['shippingOrders'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Đang giao</p>
        </a>
        <a href="<?= BASE_URL ?>/adminorder?status=completed" class="text-center p-4 rounded-xl bg-green-50 hover:bg-green-100 transition">
            <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-green-600"><?= $stats['completedOrders'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Hoàn thành</p>
        </a>
        <a href="<?= BASE_URL ?>/adminorder?status=cancelled" class="text-center p-4 rounded-xl bg-red-50 hover:bg-red-100 transition">
            <div class="w-12 h-12 bg-red-200 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-times text-red-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-red-600"><?= $stats['cancelledOrders'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Đã hủy</p>
        </a>
    </div>
</div>

<?php if (Session::isAdmin()): ?>
<!-- Charts Section - Chỉ Admin -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart - Line -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold">📈 Doanh thu 7 ngày qua</h2>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Order Status Chart - Doughnut -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold">📊 Tỷ lệ đơn hàng</h2>
        </div>
        <div class="h-64 flex items-center justify-center">
            <canvas id="orderStatusChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Category Revenue Chart - Bar -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold">🏷️ Doanh thu theo danh mục</h2>
        </div>
        <div class="h-64">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Brand Revenue Chart - Bar -->
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold">🏢 Doanh thu theo thương hiệu</h2>
        </div>
        <div class="h-64">
            <canvas id="brandChart"></canvas>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Recent Orders -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm">
        <div class="p-5 border-b flex items-center justify-between">
            <h2 class="font-bold">Đơn hàng mới nhất</h2>
            <a href="<?= BASE_URL ?>/adminorder" class="text-sm text-blue-600 hover:underline">Xem tất cả →</a>
        </div>
        <div class="overflow-x-auto">
            <?php if (empty($recentOrders)): ?>
            <p class="text-gray-500 text-center py-8">Chưa có đơn hàng nào</p>
            <?php else: ?>
            <table class="w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Mã đơn</th>
                        <th class="px-5 py-3 text-left">Khách hàng</th>
                        <th class="px-5 py-3 text-left">Tổng tiền</th>
                        <th class="px-5 py-3 text-left">Trạng thái</th>
                        <th class="px-5 py-3 text-left">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($recentOrders as $order): 
                        $status = orderStatus($order['status']);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <a href="<?= BASE_URL ?>/adminorder/detail/<?= $order['id'] ?>" class="text-blue-600 font-medium hover:underline">
                                #<?= $order['id'] ?>
                            </a>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium"><?= htmlspecialchars($order['fullname']) ?></p>
                            <p class="text-xs text-gray-500"><?= $order['phone_number'] ?></p>
                        </td>
                        <td class="px-5 py-4 font-medium text-green-600"><?= formatMoney($order['total_money']) ?></td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-600">
                                <?= $status['label'] ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500"><?= formatDateTime($order['order_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-5 border-b flex items-center justify-between">
            <h2 class="font-bold">🔥 Bán chạy nhất</h2>
            <a href="<?= BASE_URL ?>/adminproduct" class="text-sm text-blue-600 hover:underline">Xem tất cả →</a>
        </div>
        <div class="p-5">
            <?php if (empty($topProducts)): ?>
            <p class="text-gray-500 text-center py-8">Chưa có dữ liệu</p>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($topProducts as $index => $product): ?>
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        <?= $index === 0 ? 'bg-yellow-400 text-white' : ($index === 1 ? 'bg-gray-300 text-gray-700' : ($index === 2 ? 'bg-orange-400 text-white' : 'bg-gray-100 text-gray-600')) ?>">
                        <?= $index + 1 ?>
                    </span>
                    <img src="<?= productImage($product['thumbnail']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= htmlspecialchars($product['name']) ?></p>
                        <p class="text-xs text-gray-500"><?= formatMoney($product['discount_price'] ?: $product['price']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-green-600"><?= $product['sold'] ?></p>
                        <p class="text-xs text-gray-400">đã bán</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (Session::isAdmin()): ?>
<!-- Chart.js Scripts - Chỉ Admin -->
<script>
// Format số tiền VND
function formatVND(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
}

// Format ngày
function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.getDate() + '/' + (date.getMonth() + 1);
}

// 1. Biểu đồ doanh thu 7 ngày - Line Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueLabels = <?= json_encode($revenueChartLabels ?? []) ?>.map(d => formatDate(d));
const revenueData = <?= json_encode($revenueChartData ?? []) ?>;

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Doanh thu',
            data: revenueData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
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
                        return 'Doanh thu: ' + formatVND(context.raw);
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
                },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// 2. Biểu đồ tỷ lệ đơn hàng - Doughnut Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusData = <?= json_encode($orderStatusChart ?? []) ?>;

new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Chờ xác nhận', 'Đang xử lý', 'Đang giao', 'Hoàn thành', 'Đã hủy'],
        datasets: [{
            data: [
                orderStatusData.pending || 0,
                orderStatusData.processing || 0,
                orderStatusData.shipping || 0,
                orderStatusData.completed || 0,
                orderStatusData.cancelled || 0
            ],
            backgroundColor: [
                '#fbbf24', // yellow
                '#3b82f6', // blue
                '#8b5cf6', // purple
                '#10b981', // green
                '#ef4444'  // red
            ],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            }
        }
    }
});

// 3. Biểu đồ doanh thu theo danh mục - Bar Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryData = <?= json_encode($categoryChart ?? []) ?>;

new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: categoryData.map(c => c.name),
        datasets: [{
            label: 'Doanh thu',
            data: categoryData.map(c => parseFloat(c.revenue)),
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(20, 184, 166, 0.8)'
            ],
            borderRadius: 8,
            borderSkipped: false
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
                        return formatVND(context.raw);
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
                },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// 4. Biểu đồ doanh thu theo thương hiệu - Horizontal Bar Chart
const brandCtx = document.getElementById('brandChart').getContext('2d');
const brandData = <?= json_encode($brandChart ?? []) ?>;

new Chart(brandCtx, {
    type: 'bar',
    data: {
        labels: brandData.map(b => b.name),
        datasets: [{
            label: 'Doanh thu',
            data: brandData.map(b => parseFloat(b.revenue)),
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(20, 184, 166, 0.8)'
            ],
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return formatVND(context.raw);
                    }
                }
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) return (value / 1000000) + 'M';
                        if (value >= 1000) return (value / 1000) + 'K';
                        return value;
                    }
                },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            y: {
                grid: { display: false }
            }
        }
    }
});
</script>
<?php endif; ?>