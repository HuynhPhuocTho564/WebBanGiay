<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Thêm sản phẩm mới</h1>
            <p class="text-gray-500 text-sm mt-1">Điền thông tin sản phẩm bên dưới</p>
        </div>
        <a href="<?= BASE_URL ?>/adminproduct" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <form action="<?= BASE_URL ?>/adminproduct/store" method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- Thông tin cơ bản -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold text-lg mb-4 pb-2 border-b">Thông tin cơ bản</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nhập tên sản phẩm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                    <select name="category_id" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                    <select name="brand_id" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá gốc <span class="text-red-500">*</span></label>
                    <input type="number" name="price" required min="0"
                           class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="VD: 1500000">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Giá khuyến mãi</label>
                    <input type="number" name="discount_price" min="0"
                           class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Để trống nếu không giảm giá">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dành cho</label>
                    <select name="gender" class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Unisex">Nam & Nữ</option>
                        <option value="Male">Nam</option>
                        <option value="Female">Nữ</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh sản phẩm</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả sản phẩm</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Nhập mô tả chi tiết sản phẩm"></textarea>
                </div>
            </div>
        </div>

        <!-- Biến thể sản phẩm -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <h2 class="font-bold text-lg">Biến thể sản phẩm (Size & Màu)</h2>
                <button type="button" onclick="addVariant()" class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-1"></i>Thêm biến thể
                </button>
            </div>

            <div id="variants-container" class="space-y-3">
                <div class="variant-row flex items-center gap-3">
                    <input type="text" name="sizes[]" placeholder="Size (VD: 39, 40, 41)"
                           class="flex-1 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <input type="text" name="colors[]" placeholder="Màu (VD: Đen, Trắng)"
                           class="flex-1 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <input type="number" name="stocks[]" placeholder="Số lượng" min="0"
                           class="w-28 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" onclick="removeVariant(this)" class="p-2.5 text-red-500 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="<?= BASE_URL ?>/adminproduct" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Lưu sản phẩm
            </button>
        </div>
    </form>
</div>

<script>
function addVariant() {
    const container = document.getElementById('variants-container');
    const row = document.createElement('div');
    row.className = 'variant-row flex items-center gap-3';
    row.innerHTML = `
        <input type="text" name="sizes[]" placeholder="Size (VD: 39, 40, 41)"
               class="flex-1 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <input type="text" name="colors[]" placeholder="Màu (VD: Đen, Trắng)"
               class="flex-1 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <input type="number" name="stocks[]" placeholder="Số lượng" min="0"
               class="w-28 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="button" onclick="removeVariant(this)" class="p-2.5 text-red-500 hover:bg-red-50 rounded-lg transition">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
}

function removeVariant(btn) {
    const container = document.getElementById('variants-container');
    if (container.children.length > 1) {
        btn.closest('.variant-row').remove();
    }
}
</script>
