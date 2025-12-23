<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold">Quản lý danh mục</h2>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Thêm danh mục
        </button>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tên danh mục</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Slug</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Số sản phẩm</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Chưa có danh mục nào</td>
                </tr>
                <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $cat['id'] ?></td>
                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= $cat['slug'] ?></td>
                    <td class="px-4 py-3"><?= $cat['product_count'] ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full <?= $cat['status'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $cat['status'] ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)" 
                                class="text-accent hover:underline mr-3">Sửa</button>
                        <?php if ($cat['product_count'] == 0): ?>
                        <a href="<?= BASE_URL ?>/admincategory/delete/<?= $cat['id'] ?>" 
                           onclick="return confirm('Xác nhận xóa danh mục này?')"
                           class="text-red-500 hover:underline">Xóa</a>
                        <?php else: ?>
                        <button onclick="alert('Không thể xóa! Danh mục đang có <?= $cat['product_count'] ?> sản phẩm.')" 
                                class="text-gray-400 cursor-not-allowed">Xóa</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Thêm danh mục</h3>
        <form action="<?= BASE_URL ?>/admincategory/store" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Tên danh mục</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 border rounded-lg">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-accent text-white rounded-lg">Thêm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Sửa danh mục</h3>
        <form id="editForm" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Tên danh mục</label>
                <input type="text" name="name" id="editName" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="status" id="editStatus" value="1" class="rounded">
                    <span class="text-sm">Hiển thị</span>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 border rounded-lg">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-accent text-white rounded-lg">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('editName').value = cat.name;
    document.getElementById('editStatus').checked = cat.status == 1;
    document.getElementById('editForm').action = '<?= BASE_URL ?>/admincategory/update/' + cat.id;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
