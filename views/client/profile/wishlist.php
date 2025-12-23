<?php
/**
 * Trang sản phẩm yêu thích
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/views/client/profile/_sidebar.php'; ?>

        <!-- Content -->
        <div class="flex-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold mb-6">Sản phẩm yêu thích</h2>

                <?php if (empty($wishlist)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">Bạn chưa có sản phẩm yêu thích nào</p>
                    <a href="<?= BASE_URL ?>/products" class="inline-block px-6 py-2 bg-accent text-white rounded-lg hover:bg-red-600">
                        Khám phá ngay
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($wishlist as $product): ?>
                    <div class="group relative border rounded-lg overflow-hidden hover:shadow-lg transition">
                        <!-- Remove button -->
                        <button onclick="removeWishlist(<?= $product['id'] ?>, this)" 
                                class="absolute top-2 right-2 z-10 w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition">
                            <i class="fas fa-heart"></i>
                        </button>

                        <a href="<?= BASE_URL ?>/product/<?= $product['slug'] ?>">
                            <div class="aspect-square overflow-hidden">
                                <?php 
                                $imgSrc = $product['thumbnail'];
                                if (!filter_var($imgSrc, FILTER_VALIDATE_URL)) {
                                    $imgSrc = ASSETS_URL . '/images/products/' . $imgSrc;
                                }
                                ?>
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </div>
                            <div class="p-3">
                                <?php if (!empty($product['brand_name'])): ?>
                                <p class="text-xs text-gray-500 mb-1"><?= $product['brand_name'] ?></p>
                                <?php endif; ?>
                                <h3 class="font-medium text-sm line-clamp-2 mb-2"><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="flex items-center gap-2">
                                    <?php if ($product['discount_price'] > 0): ?>
                                    <span class="font-bold text-accent"><?= number_format($product['discount_price'], 0, ',', '.') ?>đ</span>
                                    <span class="text-xs text-gray-400 line-through"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                    <span class="font-bold"><?= number_format($product['price'], 0, ',', '.') ?>đ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function removeWishlist(productId, btn) {
    if (!confirm('Xóa sản phẩm khỏi danh sách yêu thích?')) return;
    
    fetch('<?= BASE_URL ?>/profile/toggleWishlist', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.closest('.group').remove();
            // Check if empty
            const grid = document.querySelector('.grid');
            if (grid && grid.children.length === 0) {
                location.reload();
            }
        }
    });
}
</script>
