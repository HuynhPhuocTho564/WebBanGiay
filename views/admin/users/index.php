<!-- Header -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold">Quản lý người dùng</h1>
        <p class="text-sm text-gray-500"><?= $totalUsers ?> người dùng</p>
    </div>
    <a href="<?= BASE_URL ?>/adminuser/create" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600 transition">
        <i class="fas fa-plus mr-2"></i> Thêm mới
    </a>
</div>

<!-- Search -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                   placeholder="Tìm theo tên, email, username..."
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
        </div>
        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-search mr-2"></i> Tìm
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Người dùng</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">SĐT</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Quyền</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Không tìm thấy người dùng nào</td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50" id="user-row-<?= $user['id'] ?>">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="<?= avatar($user['avatar']) ?>" alt="" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($user['fullname']) ?></p>
                                <p class="text-xs text-gray-500">@<?= $user['username'] ?? 'N/A' ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm"><?= $user['email'] ?></td>
                    <td class="px-4 py-3 text-sm"><?= $user['phone_number'] ?? '-' ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php 
                        $roleColors = ['gray', 'blue', 'red'];
                        $roleColor = $roleColors[$user['role']] ?? 'gray';
                        ?>
                        <span class="px-2 py-1 text-xs rounded-full bg-<?= $roleColor ?>-100 text-<?= $roleColor ?>-600">
                            <?= roleName($user['role']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="toggleStatus(<?= $user['id'] ?>)" 
                                class="status-btn-<?= $user['id'] ?> px-2 py-1 text-xs rounded-full <?= $user['status'] == 1 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                            <?= $user['status'] == 1 ? 'Hoạt động' : 'Đã khóa' ?>
                        </button>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= BASE_URL ?>/adminuser/edit/<?= $user['id'] ?>" 
                               class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($user['id'] !== Session::userId()): ?>
                            <button onclick="deleteUser(<?= $user['id'] ?>)" 
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Trang <?= $currentPage ?> / <?= $totalPages ?>
        </p>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
               class="px-3 py-1 rounded <?= $i === $currentPage ? 'bg-accent text-white' : 'hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleStatus(userId) {
    if (!confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này?')) return;
    
    fetch('<?= BASE_URL ?>/adminuser/toggleStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + userId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector('.status-btn-' + userId);
            if (data.newStatus == 1) {
                btn.className = 'status-btn-' + userId + ' px-2 py-1 text-xs rounded-full bg-green-100 text-green-600';
                btn.textContent = 'Hoạt động';
            } else {
                btn.className = 'status-btn-' + userId + ' px-2 py-1 text-xs rounded-full bg-red-100 text-red-600';
                btn.textContent = 'Đã khóa';
            }
        }
        alert(data.message);
    });
}

function deleteUser(userId) {
    if (!confirm('Bạn có chắc muốn xóa người dùng này? Hành động này không thể hoàn tác!')) return;
    
    fetch('<?= BASE_URL ?>/adminuser/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + userId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('user-row-' + userId).remove();
        }
        alert(data.message);
    });
}
</script>
