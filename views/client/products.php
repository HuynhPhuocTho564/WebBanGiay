<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="<?= BASE_URL ?>" class="hover:text-accent">Trang chủ</a></li>
            <li>/</li>
            <li class="text-primary font-medium">Sản phẩm</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Filter -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl p-4 shadow-sm sticky top-24 max-h-[calc(100vh-120px)] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Bộ lọc</h3>
                    <a href="<?= BASE_URL ?>/home/products" class="text-sm text-accent hover:underline">Xóa lọc</a>
                </div>

                <form id="filterForm" method="GET" action="<?= BASE_URL ?>/home/products">
                    <!-- Search -->
                    <?php if (!empty($filters['search'])): ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($filters['search']) ?>">
                    <?php endif; ?>

                    <!-- Category -->
                    <div class="mb-5">
                        <h4 class="font-medium mb-3">Danh mục</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                            <?php 
                            $selectedCategories = $filters['category_id'] ?? [];
                            if (!is_array($selectedCategories)) $selectedCategories = $selectedCategories ? [$selectedCategories] : [];
                            ?>
                            <?php foreach ($categories as $cat): ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="category[]" value="<?= $cat['id'] ?>" 
                                       <?= in_array($cat['id'], $selectedCategories) ? 'checked' : '' ?>
                                       class="w-4 h-4 rounded text-accent focus:ring-accent"
                                       onchange="this.form.submit()">
                                <span class="text-sm"><?= $cat['name'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Brand -->
                    <div class="mb-5">
                        <h4 class="font-medium mb-3">Thương hiệu</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                            <?php 
                            $selectedBrands = $filters['brand_id'] ?? [];
                            if (!is_array($selectedBrands)) $selectedBrands = $selectedBrands ? [$selectedBrands] : [];
                            ?>
                            <?php foreach ($brands as $brand): ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="brand[]" value="<?= $brand['id'] ?>"
                                       <?= in_array($brand['id'], $selectedBrands) ? 'checked' : '' ?>
                                       class="w-4 h-4 rounded text-accent focus:ring-accent"
                                       onchange="this.form.submit()">
                                <span class="text-sm"><?= $brand['name'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="mb-5">
                        <h4 class="font-medium mb-3">Giới tính</h4>
                        <div class="flex flex-wrap gap-2" id="genderFilter">
                            <input type="hidden" name="gender" id="genderInput" value="<?= $filters['gender'] ?? '' ?>">
                            <button type="button" data-gender="Male" 
                                    class="gender-btn px-3 py-1.5 border rounded-full text-sm transition <?= ($filters['gender'] ?? '') === 'Male' ? 'bg-primary text-white border-primary' : '' ?>">
                                Nam
                            </button>
                            <button type="button" data-gender="Female"
                                    class="gender-btn px-3 py-1.5 border rounded-full text-sm transition <?= ($filters['gender'] ?? '') === 'Female' ? 'bg-primary text-white border-primary' : '' ?>">
                                Nữ
                            </button>
                            <button type="button" data-gender="Unisex"
                                    class="gender-btn px-3 py-1.5 border rounded-full text-sm transition <?= ($filters['gender'] ?? '') === 'Unisex' ? 'bg-primary text-white border-primary' : '' ?>">
                                Unisex
                            </button>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-5">
                        <h4 class="font-medium mb-3">Khoảng giá</h4>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" placeholder="Từ" min="0"
                                   value="<?= $filters['min_price'] ?? '' ?>"
                                   class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-accent">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Đến" min="0"
                                   value="<?= $filters['max_price'] ?? '' ?>"
                                   class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-accent">
                        </div>
                    </div>

                    <!-- Apply Button -->
                    <button type="submit" class="w-full py-2.5 bg-primary text-white rounded-lg text-sm font-medium hover:bg-orange-500 transition">
                        Áp dụng bộ lọc
                    </button>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl font-bold">
                        <?php if (!empty($filters['search'])): ?>
                            Kết quả tìm kiếm: "<?= htmlspecialchars($filters['search']) ?>"
                        <?php else: ?>
                            Tất cả sản phẩm
                        <?php endif; ?>
                    </h1>
                    <p class="text-sm text-gray-500"><?= $totalProducts ?> sản phẩm</p>
                </div>

                <!-- Sort -->
                <select name="sort" onchange="window.location.href=this.value"
                        class="px-4 py-2 border rounded-lg text-sm focus:outline-none focus:border-accent">
                    <option value="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['sort' => 'newest'])) ?>"
                            <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['sort' => 'price_asc'])) ?>"
                            <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Giá thấp đến cao</option>
                    <option value="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['sort' => 'price_desc'])) ?>"
                            <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Giá cao đến thấp</option>
                    <option value="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['sort' => 'popular'])) ?>"
                            <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Phổ biến nhất</option>
                </select>
            </div>

            <!-- Products -->
            <?php if (empty($products)): ?>
            <div class="text-center py-16">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Không tìm thấy sản phẩm nào</p>
                <a href="<?= BASE_URL ?>/home/products" class="inline-block mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-accent transition">
                    Xem tất cả sản phẩm
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                <?php foreach ($products as $product): ?>
                <?php include BASE_PATH . '/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-center gap-2 mt-8">
                <?php if ($currentPage > 1): ?>
                <a href="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>"
                   class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-100">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <a href="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"
                   class="w-10 h-10 flex items-center justify-center border rounded-lg <?= $i === $currentPage ? 'bg-primary text-white' : 'hover:bg-gray-100' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                <a href="<?= BASE_URL ?>/home/products?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>"
                   class="w-10 h-10 flex items-center justify-center border rounded-lg hover:bg-gray-100">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Mobile Filter Button -->
<button id="mobileFilterBtn" class="lg:hidden fixed bottom-6 left-6 w-14 h-14 bg-primary text-white rounded-full shadow-lg z-30">
    <i class="fas fa-filter"></i>
</button>

<script>
// Mobile filter toggle
document.getElementById('mobileFilterBtn')?.addEventListener('click', function() {
    const sidebar = document.querySelector('aside');
    sidebar.classList.toggle('fixed');
    sidebar.classList.toggle('inset-0');
    sidebar.classList.toggle('z-50');
    sidebar.classList.toggle('bg-white');
    sidebar.classList.toggle('overflow-y-auto');
});

// Gender filter toggle
document.querySelectorAll('.gender-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const gender = this.dataset.gender;
        const input = document.getElementById('genderInput');
        const form = document.getElementById('filterForm');
        
        // Nếu đang chọn thì bỏ chọn, ngược lại thì chọn
        if (input.value === gender) {
            input.value = '';
        } else {
            input.value = gender;
        }
        
        form.submit();
    });
});
</script>
