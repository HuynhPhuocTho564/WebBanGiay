<?php $actionInfo = actionLabel($log['action']); ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/adminaudit" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold">Chi tiết hoạt động #<?= $log['id'] ?></h1>
            <p class="text-gray-500 text-sm"><?= formatDateTime($log['created_at']) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Action Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Thông tin hành động</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-<?= $actionInfo['color'] ?>-100 flex items-center justify-center">
                            <i class="fas fa-<?= $actionInfo['icon'] ?> text-<?= $actionInfo['color'] ?>-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-<?= $actionInfo['color'] ?>-100 text-<?= $actionInfo['color'] ?>-700">
                                <?= $actionInfo['label'] ?>
                            </span>
                            <?php if ($log['entity_type']): ?>
                            <p class="text-gray-500 text-sm mt-1">
                                <?= entityTypeLabel($log['entity_type']) ?>
                                <?php if ($log['entity_id']): ?>#<?= $log['entity_id'] ?><?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <label class="text-sm text-gray-500">Mô tả</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($log['description']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Data Changes -->
            <?php if ($log['old_data'] || $log['new_data']): ?>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Thay đổi dữ liệu</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if ($log['old_data']): ?>
                    <div>
                        <label class="text-sm text-gray-500 flex items-center gap-2 mb-2">
                            <i class="fas fa-minus-circle text-red-500"></i> Dữ liệu cũ
                        </label>
                        <pre class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm overflow-x-auto max-h-64"><?= htmlspecialchars(json_encode(json_decode($log['old_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                    </div>
                    <?php endif; ?>

                    <?php if ($log['new_data']): ?>
                    <div>
                        <label class="text-sm text-gray-500 flex items-center gap-2 mb-2">
                            <i class="fas fa-plus-circle text-green-500"></i> Dữ liệu mới
                        </label>
                        <pre class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm overflow-x-auto max-h-64"><?= htmlspecialchars(json_encode(json_decode($log['new_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Người thực hiện</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <div>
                            <p class="font-medium"><?= htmlspecialchars($log['username'] ?? 'Guest') ?></p>
                            <?php if ($log['user_id']): ?>
                            <p class="text-sm text-gray-500">User ID: <?= $log['user_id'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Thông tin kỹ thuật</h2>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="text-gray-500">Thời gian</label>
                        <p class="font-medium"><?= formatDateTime($log['created_at']) ?></p>
                    </div>
                    
                    <div>
                        <label class="text-gray-500">Địa chỉ IP</label>
                        <p class="font-medium"><?= $log['ip_address'] ?? 'N/A' ?></p>
                    </div>
                    
                    <?php if ($log['user_agent']): ?>
                    <div>
                        <label class="text-gray-500">User Agent</label>
                        <p class="text-xs text-gray-600 break-all"><?= htmlspecialchars(truncate($log['user_agent'], 150)) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
