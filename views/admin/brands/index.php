<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold">Quản lý thương hiệu</h2>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Thêm thương hiệu
        </button>
    </div>

    <!-- Brands Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">ID</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tên thương hiệu</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Slug</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Số sản phẩm</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Trạng thái</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($brands)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Chưa có thương hiệu nào</td>
                </tr>
                <?php else: ?>
                <?php foreach ($brands as $brand): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $brand['id'] ?></td>
                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($brand['name']) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= $brand['slug'] ?></td>
                    <td class="px-4 py-3"><?= $brand['product_count'] ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full <?= ($brand['status'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= ($brand['status'] ?? 1) ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="editBrand(<?= htmlspecialchars(json_encode($brand)) ?>)" class="text-accent hover:underline mr-2">Sửa</button>
                        <?php if ($brand['product_count'] == 0): ?>
                        <a href="<?= BASE_URL ?>/adminbrand/delete/<?= $brand['id'] ?>" onclick="return confirm('Xác nhận xóa?')" class="text-red-500 hover:underline">Xóa</a>
                        <?php else: ?>
                        <button onclick="alert('Không thể xóa! Đang có <?= $brand['product_count'] ?> sản phẩm.')" class="text-gray-400">Xóa</button>
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
        <h3 class="text-lg font-bold mb-4">Thêm thương hiệu</h3>
        <form action="<?= BASE_URL ?>/adminbrand/store" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Tên thương hiệu</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 px-4 py-2 border rounded-lg">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-accent text-white rounded-lg">Thêm</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg w-full max-w-md p-6">
        <h3 class="text-lg font-bold mb-4">Sửa thương hiệu</h3>
        <form id="editForm" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Tên thương hiệu</label>
                <input type="text" name="name" id="editName" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="status" id="editStatus" value="1" class="rounded">
                    <span class="text-sm">Hiển thị thương hiệu</span>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 px-4 py-2 border rounded-lg">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-accent text-white rounded-lg">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
function editBrand(brand) {
    document.getElementById('editName').value = brand.name;
    document.getElementById('editStatus').checked = (brand.status ?? 1) == 1;
    document.getElementById('editForm').action = '<?= BASE_URL ?>/adminbrand/update/' + brand.id;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
