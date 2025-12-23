<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Sửa sản phẩm</h1>
            <p class="text-gray-500 text-sm mt-1"><?= htmlspecialchars($product['name']) ?></p>
        </div>
        <a href="<?= BASE_URL ?>/adminproduct" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <form action="<?= BASE_URL ?>/adminproduct/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- Thông tin cơ bản -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold text-lg mb-4 pb-2 border-b">Thông tin cơ bản</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name']) ?>"
                           class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                    <select name="category_id" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                    <select name="brand_id" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>" <?= $product['brand_id'] == $brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá gốc <span class="text-red-500">*</span></label>
                    <input type="number" name="price" required min="0" value="<?= $product['price'] ?>"
                           class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá khuyến mãi</label>
                    <input type="number" name="discount_price" min="0" value="<?= $product['discount_price'] ?>"
                           class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giới tính</label>
                    <select name="gender" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="unisex" <?= $product['gender'] == 'unisex' ? 'selected' : '' ?>>Unisex</option>
                        <option value="male" <?= $product['gender'] == 'male' ? 'selected' : '' ?>>Nam</option>
                        <option value="female" <?= $product['gender'] == 'female' ? 'selected' : '' ?>>Nữ</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh sản phẩm</label>
                    <?php if ($product['thumbnail']): ?>
                    <div class="mb-2">
                        <img src="<?= productImage($product['thumbnail']) ?>" alt="" class="w-20 h-20 object-cover rounded-lg">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="thumbnail" accept="image/*"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Để trống nếu không muốn thay đổi ảnh</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả sản phẩm</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Biến thể hiện tại -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold text-lg mb-4 pb-2 border-b">Biến thể sản phẩm hiện tại</h2>
            
            <?php if (empty($variants)): ?>
            <p class="text-gray-500 text-center py-4">Chưa có biến thể nào</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Màu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($variants as $variant): ?>
                        <tr>
                            <td class="px-4 py-3"><?= htmlspecialchars($variant['size']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($variant['color']) ?></td>
                            <td class="px-4 py-3"><?= $variant['stock_quantity'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="<?= BASE_URL ?>/adminproduct" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Cập nhật
            </button>
        </div>
    </form>
</div>
