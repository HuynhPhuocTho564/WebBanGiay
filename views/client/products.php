<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="<?= BASE_URL ?>" class="hover:text-accent">Trang chủ</a></li>
            <li>/</li>
            <li class="text-primary font-medium">Sản phẩm</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6 lg:items-start">
        <!-- Sidebar Filter -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-xl p-4 shadow-sm lg:sticky lg:top-24">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Bộ lọc</h3>
                    <a href="<?= BASE_URL ?>/home/products" class="text-sm text-accent hover:underline">Xóa lọc</a>
                </div>

                <form id="filterForm" method="GET" action="<?= BASE_URL ?>/home/products">
                    <!-- Search -->
                    <?php if (!empty($filters['search'])): ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($filters['search']) ?>">
                    <?php endif; ?>

                    <!-- Category - Collapsible -->
                    <?php 
                    $selectedCategories = $filters['category_id'] ?? [];
                    if (!is_array($selectedCategories)) $selectedCategories = $selectedCategories ? [$selectedCategories] : [];
                    $hasCategoryFilter = !empty($selectedCategories);
                    ?>
                    <div class="border-b pb-3 mb-3">
                        <button type="button" class="filter-toggle w-full flex items-center justify-between py-2 font-medium" data-target="categorySection">
                            <span>Danh mục <?= $hasCategoryFilter ? '<span class="text-accent text-xs">(' . count($selectedCategories) . ')</span>' : '' ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <div id="categorySection" class="filter-content mt-2 space-y-2">
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

                    <!-- Brand - Collapsible -->
                    <?php 
                    $selectedBrands = $filters['brand_id'] ?? [];
                    if (!is_array($selectedBrands)) $selectedBrands = $selectedBrands ? [$selectedBrands] : [];
                    $hasBrandFilter = !empty($selectedBrands);
                    ?>
                    <div class="border-b pb-3 mb-3">
                        <button type="button" class="filter-toggle w-full flex items-center justify-between py-2 font-medium" data-target="brandSection">
                            <span>Thương hiệu <?= $hasBrandFilter ? '<span class="text-accent text-xs">(' . count($selectedBrands) . ')</span>' : '' ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <div id="brandSection" class="filter-content mt-2 space-y-2">
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

                    <!-- Gender - Collapsible -->
                    <?php $hasGenderFilter = !empty($filters['gender']); ?>
                    <div class="border-b pb-3 mb-3">
                        <button type="button" class="filter-toggle w-full flex items-center justify-between py-2 font-medium" data-target="genderSection">
                            <span>Dành cho <?= $hasGenderFilter ? '<span class="text-accent text-xs">(1)</span>' : '' ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <div id="genderSection" class="filter-content mt-2">
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
                                    Nam & Nữ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Price Range - Collapsible -->
                    <?php 
                    $currentMin = $filters['min_price'] ?? 0;
                    $currentMax = $filters['max_price'] ?? 5000000;
                    $hasPriceFilter = !empty($filters['min_price']) || !empty($filters['max_price']);
                    $minPrice = 0;
                    $maxPrice = 5000000;
                    ?>
                    <div class="pb-3 mb-3">
                        <button type="button" class="filter-toggle w-full flex items-center justify-between py-2 font-medium" data-target="priceSection">
                            <span>Khoảng giá <?= $hasPriceFilter ? '<span class="text-accent text-xs">(đã chọn)</span>' : '' ?></span>
                            <i class="fas fa-chevron-down text-xs transition-transform"></i>
                        </button>
                        <div id="priceSection" class="filter-content mt-2">
                            <!-- Khoảng giá có sẵn -->
                            <div class="space-y-3 mb-5">
                                <?php 
                                $priceRanges = [
                                    ['min' => 0, 'max' => 1500000, 'label' => 'Dưới 1,500,000đ'],
                                    ['min' => 1500000, 'max' => 2500000, 'label' => '1,500,000đ - 2,500,000đ'],
                                    ['min' => 2500000, 'max' => 3500000, 'label' => '2,500,000đ - 3,500,000đ'],
                                    ['min' => 3500000, 'max' => 5000000, 'label' => 'Trên 3,500,000đ'],
                                ];
                                foreach ($priceRanges as $range):
                                    $isSelected = ($currentMin == $range['min'] && $currentMax == $range['max']);
                                ?>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="price_range" 
                                           value="<?= $range['min'] ?>-<?= $range['max'] ?>"
                                           <?= $isSelected ? 'checked' : '' ?>
                                           class="w-4 h-4 text-accent focus:ring-accent"
                                           onchange="setPriceRange(<?= $range['min'] ?>, <?= $range['max'] ?>)">
                                    <span class="text-base"><?= $range['label'] ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Nhập tùy chỉnh -->
                            <p class="text-sm text-gray-500 mb-3">Hoặc tùy chỉnh:</p>
                            
                            <!-- Range Slider -->
                            <div class="mb-2">
                                <div class="range-slider-container">
                                    <div class="range-track"></div>
                                    <div class="range-selected" id="rangeSelected"></div>
                                    <input type="range" id="rangeMin" min="<?= $minPrice ?>" max="<?= $maxPrice ?>" 
                                           value="<?= $currentMin ?>" step="100000" class="range-input">
                                    <input type="range" id="rangeMax" min="<?= $minPrice ?>" max="<?= $maxPrice ?>" 
                                           value="<?= $currentMax ?>" step="100000" class="range-input">
                                </div>
                                <div class="flex justify-between text-sm text-gray-500 mt-2">
                                    <span>0đ</span>
                                    <span>5,000,000đ</span>
                                </div>
                            </div>
                            
                            <!-- Input fields -->
                            <div class="flex items-center gap-2 mt-4">
                                <input type="text" name="min_price" id="minPriceInput" placeholder="Từ"
                                       value="<?= !empty($filters['min_price']) ? number_format($filters['min_price'], 0, ',', ',') : '' ?>"
                                       class="w-full px-3 py-2.5 border rounded-lg text-base focus:outline-none focus:border-accent"
                                       oninput="formatPriceInput(this); syncSliderFromInput()">
                                <span class="text-gray-400">-</span>
                                <input type="text" name="max_price" id="maxPriceInput" placeholder="Đến"
                                       value="<?= !empty($filters['max_price']) ? number_format($filters['max_price'], 0, ',', ',') : '' ?>"
                                       class="w-full px-3 py-2.5 border rounded-lg text-base focus:outline-none focus:border-accent"
                                       oninput="formatPriceInput(this); syncSliderFromInput()">
                            </div>
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

<style>
/* Collapsible filter sections */
.filter-content {
    max-height: 500px;
    overflow: hidden;
    transition: max-height 0.3s ease;
}
.filter-content.collapsed {
    max-height: 0;
    margin-top: 0 !important;
}
.filter-toggle i {
    transition: transform 0.3s ease;
}
.filter-toggle.collapsed i {
    transform: rotate(-90deg);
}

/* Range Slider */
.range-slider-container {
    position: relative;
    height: 30px;
    margin: 10px 0;
}
.range-track {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
}
.range-selected {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    height: 4px;
    background: linear-gradient(to right, #ef4444, #f97316);
    border-radius: 2px;
}
.range-input {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    pointer-events: none;
    margin: 0;
}
.range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: #ef4444;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: auto;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.range-input::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: #ef4444;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: auto;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
</style>

<script>
// Collapsible filter sections
document.querySelectorAll('.filter-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const content = document.getElementById(targetId);
        
        this.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    });
});

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

// Price range selection
function setPriceRange(min, max) {
    const minInput = document.getElementById('minPriceInput');
    const maxInput = document.getElementById('maxPriceInput');
    const rangeMin = document.getElementById('rangeMin');
    const rangeMax = document.getElementById('rangeMax');
    
    minInput.value = min >= 0 ? formatNumber(min) : '';
    maxInput.value = max > 0 ? formatNumber(max) : '';
    
    // Update slider
    if (rangeMin && rangeMax) {
        rangeMin.value = min;
        rangeMax.value = max > 0 ? max : 5000000;
        updateRangeSlider();
    }
}

// Range Slider
const rangeMin = document.getElementById('rangeMin');
const rangeMax = document.getElementById('rangeMax');
const rangeSelected = document.getElementById('rangeSelected');

function updateRangeSlider() {
    if (!rangeMin || !rangeMax || !rangeSelected) return;
    
    const minVal = parseInt(rangeMin.value);
    const maxVal = parseInt(rangeMax.value);
    const minPercent = (minVal / 5000000) * 100;
    const maxPercent = (maxVal / 5000000) * 100;
    
    rangeSelected.style.left = minPercent + '%';
    rangeSelected.style.width = (maxPercent - minPercent) + '%';
    
    // Update input fields
    document.getElementById('minPriceInput').value = formatNumber(minVal);
    document.getElementById('maxPriceInput').value = formatNumber(maxVal);
    
    // Uncheck radio buttons
    document.querySelectorAll('input[name="price_range"]').forEach(r => r.checked = false);
}

if (rangeMin && rangeMax) {
    rangeMin.addEventListener('input', function() {
        if (parseInt(rangeMin.value) > parseInt(rangeMax.value)) {
            rangeMin.value = rangeMax.value;
        }
        updateRangeSlider();
    });
    
    rangeMax.addEventListener('input', function() {
        if (parseInt(rangeMax.value) < parseInt(rangeMin.value)) {
            rangeMax.value = rangeMin.value;
        }
        updateRangeSlider();
    });
    
    // Initialize slider position
    updateRangeSlider();
}

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Format price input while typing
function formatPriceInput(input) {
    let value = input.value.replace(/[^\d]/g, '');
    if (value) {
        input.value = formatNumber(parseInt(value));
    }
    document.querySelectorAll('input[name="price_range"]').forEach(r => r.checked = false);
}

// Sync slider from input fields
function syncSliderFromInput() {
    const minInput = document.getElementById('minPriceInput');
    const maxInput = document.getElementById('maxPriceInput');
    const rangeMin = document.getElementById('rangeMin');
    const rangeMax = document.getElementById('rangeMax');
    
    if (!rangeMin || !rangeMax) return;
    
    let minVal = parseInt(minInput.value.replace(/,/g, '')) || 0;
    let maxVal = parseInt(maxInput.value.replace(/,/g, '')) || 5000000;
    
    // Clamp values
    minVal = Math.max(0, Math.min(minVal, 5000000));
    maxVal = Math.max(0, Math.min(maxVal, 5000000));
    
    rangeMin.value = minVal;
    rangeMax.value = maxVal;
    
    // Update visual
    const minPercent = (minVal / 5000000) * 100;
    const maxPercent = (maxVal / 5000000) * 100;
    rangeSelected.style.left = minPercent + '%';
    rangeSelected.style.width = (maxPercent - minPercent) + '%';
}

// Clear radio when custom price is entered
document.getElementById('minPriceInput')?.addEventListener('focus', function() {
    document.querySelectorAll('input[name="price_range"]').forEach(r => r.checked = false);
});
document.getElementById('maxPriceInput')?.addEventListener('focus', function() {
    document.querySelectorAll('input[name="price_range"]').forEach(r => r.checked = false);
});

// Convert formatted price to number before submit
document.getElementById('filterForm')?.addEventListener('submit', function() {
    const minInput = document.getElementById('minPriceInput');
    const maxInput = document.getElementById('maxPriceInput');
    if (minInput.value) minInput.value = minInput.value.replace(/,/g, '');
    if (maxInput.value) maxInput.value = maxInput.value.replace(/,/g, '');
});
</script>
