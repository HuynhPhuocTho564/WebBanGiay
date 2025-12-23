<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold">Quản lý sản phẩm</h2>
        <a href="<?= BASE_URL ?>/adminproduct/create" class="px-4 py-2 bg-accent text-white rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="q" placeholder="Tìm sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   class="px-4 py-2 border rounded-lg focus:outline-none focus:border-accent">
            <select name="category" class="px-4 py-2 border rounded-lg">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($_GET['category'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <select name="brand" class="px-4 py-2 border rounded-lg">
                <option value="">Tất cả thương hiệu</option>
                <?php foreach ($brands as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($_GET['brand'] ?? '') == $b['id'] ? 'selected' : '' ?>><?= $b['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg"><i class="fas fa-search mr-2"></i>Lọc</button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Sản phẩm</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Danh mục</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Giá</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tồn kho</th>
                    <th class="px-4 py-3 text-center text-sm font-medium text-gray-600">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($products)): ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Không có sản phẩm</td></tr>
                <?php else: foreach ($products as $p): 
                    $img = filter_var($p['thumbnail'], FILTER_VALIDATE_URL) ? $p['thumbnail'] : ASSETS_URL.'/images/products/'.$p['thumbnail'];
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="<?= $img ?>" class="w-12 h-12 object-cover rounded">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="text-xs text-gray-500"><?= $p['brand_name'] ?? '' ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3"><?= $p['category_name'] ?? '-' ?></td>
                    <td class="px-4 py-3"><?= number_format($p['price'], 0, ',', '.') ?>đ</td>
                    <td class="px-4 py-3"><?= $p['total_stock'] ?? 0 ?></td>
                    <td class="px-4 py-3 text-center">
                        <a href="<?= BASE_URL ?>/adminproduct/edit/<?= $p['id'] ?>" class="text-accent hover:underline mr-3">Sửa</a>
                        <a href="<?= BASE_URL ?>/adminproduct/delete/<?= $p['id'] ?>" onclick="return confirm('Xác nhận xóa?')" class="text-red-500 hover:underline">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
