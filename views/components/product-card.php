<?php
/**
 * Product Card Component
 * Sử dụng: include với biến $product
 */
$hasDiscount = $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
$finalPrice = $hasDiscount ? $product['discount_price'] : $product['price'];
$isOutOfStock = isset($product['total_stock']) && $product['total_stock'] <= 0;
?>

<div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition">
    <!-- Image -->
    <a href="<?= BASE_URL ?>/home/product/<?= $product['slug'] ?>" class="block relative aspect-square overflow-hidden">
        <img src="<?= productImage($product['thumbnail']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>"
             loading="lazy"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        
        <!-- Badges -->
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            <?php if ($hasDiscount): ?>
            <span class="bg-accent text-white text-xs px-2 py-1 rounded">
                -<?= discountPercent($product['price'], $product['discount_price']) ?>%
            </span>
            <?php endif; ?>
            
            <?php if ($isOutOfStock): ?>
            <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">Hết hàng</span>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition">
            <?php if (Session::isLoggedIn()): ?>
            <button onclick="addToWishlist(<?= $product['id'] ?>)" 
                    class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:bg-accent hover:text-white transition">
                <i class="far fa-heart text-sm"></i>
            </button>
            <?php endif; ?>
            <button onclick="quickView(<?= $product['id'] ?>)" 
                    class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:bg-accent hover:text-white transition">
                <i class="far fa-eye text-sm"></i>
            </button>
        </div>
    </a>

    <!-- Info -->
    <div class="p-3 md:p-4">
        <!-- Brand -->
        <?php if (!empty($product['brand_name'])): ?>
        <p class="text-xs text-gray-400 uppercase mb-1"><?= $product['brand_name'] ?></p>
        <?php endif; ?>

        <!-- Name -->
        <a href="<?= BASE_URL ?>/home/product/<?= $product['slug'] ?>" 
           class="block font-medium text-sm md:text-base mb-2 line-clamp-2 hover:text-accent transition">
            <?= htmlspecialchars($product['name']) ?>
        </a>

        <!-- Price -->
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-accent font-bold"><?= formatMoney($finalPrice) ?></span>
            <?php if ($hasDiscount): ?>
            <span class="text-gray-400 text-sm line-through"><?= formatMoney($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Add to Cart Button (Mobile friendly) -->
        <?php if (!$isOutOfStock): ?>
        <a href="<?= BASE_URL ?>/home/product/<?= $product['slug'] ?>" 
           class="mt-3 w-full py-2 bg-primary text-white text-sm rounded-lg text-center block md:hidden">
            Xem chi tiết
        </a>
        <button onclick="window.location.href='<?= BASE_URL ?>/home/product/<?= $product['slug'] ?>'" 
                class="mt-3 w-full py-2 bg-primary text-white text-sm rounded-lg hover:bg-accent transition hidden md:block">
            <i class="fas fa-shopping-bag mr-1"></i> Thêm vào giỏ
        </button>
        <?php else: ?>
        <button disabled class="mt-3 w-full py-2 bg-gray-300 text-gray-500 text-sm rounded-lg cursor-not-allowed">
            Hết hàng
        </button>
        <?php endif; ?>
    </div>
</div>
