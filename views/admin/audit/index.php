<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Lịch sử hoạt động</h1>
            <p class="text-gray-500 text-sm mt-1">Theo dõi tất cả hoạt động trong hệ thống</p>
        </div>
        
        <!-- Cleanup Button -->
        <form action="<?= BASE_URL ?>/adminaudit/cleanup" method="POST" 
              onsubmit="return confirm('Bạn có chắc muốn xóa các bản ghi cũ?')">
            <?= csrfField() ?>
            <input type="hidden" name="days" value="90">
            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                <i class="fas fa-broom mr-2"></i>Xóa log > 90 ngày
            </button>
        </form>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" 
                       placeholder="Tìm kiếm..." class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <!-- Action Filter -->
            <div>
                <select name="action" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Hành động --</option>
                    <?php foreach ($actions as $a): ?>
                    <option value="<?= $a['action'] ?>" <?= $filters['action'] === $a['action'] ? 'selected' : '' ?>>
                        <?= actionLabel($a['action'])['label'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Entity Type Filter -->
            <div>
                <select name="entity_type" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Loại đối tượng --</option>
                    <?php foreach ($entityTypes as $et): ?>
                    <option value="<?= $et['entity_type'] ?>" <?= $filters['entity_type'] === $et['entity_type'] ? 'selected' : '' ?>>
                        <?= entityTypeLabel($et['entity_type']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- User Filter -->
            <div>
                <select name="user_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Người thực hiện --</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $filters['user_id'] == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['fullname']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" 
                       class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Từ ngày">
            </div>

            <!-- Date To -->
            <div class="flex gap-2">
                <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" 
                       class="flex-1 px-3 py-2 border rounded-lg text-sm" placeholder="Đến ngày">
                <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
                    <i class="fas fa-search"></i>
                </button>
                <a href="<?= BASE_URL ?>/adminaudit" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="flex items-center gap-4 text-sm text-gray-600">
        <span><i class="fas fa-list mr-1"></i> Tổng: <strong><?= number_format($total) ?></strong> bản ghi</span>
        <span>|</span>
        <span>Trang <?= $currentPage ?>/<?= max(1, $totalPages) ?></span>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-left text-sm">
                    <tr>
                        <th class="px-4 py-3 font-medium">Thời gian</th>
                        <th class="px-4 py-3 font-medium">Người thực hiện</th>
                        <th class="px-4 py-3 font-medium">Hành động</th>
                        <th class="px-4 py-3 font-medium">Đối tượng</th>
                        <th class="px-4 py-3 font-medium">Mô tả</th>
                        <th class="px-4 py-3 font-medium">IP</th>
                        <th class="px-4 py-3 font-medium w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-history text-4xl mb-2"></i>
                            <p>Chưa có lịch sử hoạt động</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($logs as $log): 
                        $actionInfo = actionLabel($log['action']);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <div class="text-gray-900"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                            <div class="text-gray-500 text-xs"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium"><?= htmlspecialchars($log['username'] ?? 'Guest') ?></div>
                            <?php if ($log['user_id']): ?>
                            <div class="text-xs text-gray-500">ID: <?= $log['user_id'] ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-<?= $actionInfo['color'] ?>-100 text-<?= $actionInfo['color'] ?>-700">
                                <i class="fas fa-<?= $actionInfo['icon'] ?>"></i>
                                <?= $actionInfo['label'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if ($log['entity_type']): ?>
                            <span class="text-gray-700"><?= entityTypeLabel($log['entity_type']) ?></span>
                            <?php if ($log['entity_id']): ?>
                            <span class="text-gray-500">#<?= $log['entity_id'] ?></span>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="<?= htmlspecialchars($log['description']) ?>">
                            <?= htmlspecialchars(truncate($log['description'], 50)) ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?= $log['ip_address'] ?? '-' ?>
                        </td>
                        <td class="px-4 py-3">
                            <a href="<?= BASE_URL ?>/adminaudit/detail/<?= $log['id'] ?>" 
                               class="text-accent hover:text-blue-700" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="flex justify-center gap-1">
        <?php 
        $queryParams = $filters;
        unset($queryParams['page']);
        $queryString = http_build_query(array_filter($queryParams));
        ?>
        
        <?php if ($currentPage > 1): ?>
        <a href="<?= BASE_URL ?>/adminaudit?page=<?= $currentPage - 1 ?>&<?= $queryString ?>" 
           class="px-3 py-2 bg-white border rounded-lg hover:bg-gray-50">
            <i class="fas fa-chevron-left"></i>
        </a>
        <?php endif; ?>

        <?php 
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);
        ?>

        <?php if ($start > 1): ?>
        <a href="<?= BASE_URL ?>/adminaudit?page=1&<?= $queryString ?>" class="px-3 py-2 bg-white border rounded-lg hover:bg-gray-50">1</a>
        <?php if ($start > 2): ?><span class="px-2 py-2">...</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
        <a href="<?= BASE_URL ?>/adminaudit?page=<?= $i ?>&<?= $queryString ?>" 
           class="px-3 py-2 border rounded-lg <?= $i === $currentPage ? 'bg-accent text-white' : 'bg-white hover:bg-gray-50' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>

        <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?><span class="px-2 py-2">...</span><?php endif; ?>
        <a href="<?= BASE_URL ?>/adminaudit?page=<?= $totalPages ?>&<?= $queryString ?>" class="px-3 py-2 bg-white border rounded-lg hover:bg-gray-50"><?= $totalPages ?></a>
        <?php endif; ?>

        <?php if ($currentPage < $totalPages): ?>
        <a href="<?= BASE_URL ?>/adminaudit?page=<?= $currentPage + 1 ?>&<?= $queryString ?>" 
           class="px-3 py-2 bg-white border rounded-lg hover:bg-gray-50">
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
