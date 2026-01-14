<?php
/**
 * Product Card Component
 * Sử dụng: include với biến $product
 */
$hasDiscount = $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
$finalPrice = $hasDiscount ? $product['discount_price'] : $product['price'];
$isOutOfStock = isset($product['total_stock']) && $product['total_stock'] <= 0;
?>

<a href="<?= BASE_URL ?>/home/product/<?= $product['slug'] ?>" class="group block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
    <!-- Image -->
    <div class="relative aspect-square overflow-hidden bg-gray-100">
        <img src="<?= productImage($product['thumbnail']) ?>" 
             alt="<?= htmlspecialchars($product['name']) ?>"
             loading="lazy"
             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        
        <!-- Overlay gradient on hover -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
        
        <!-- Badges -->
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            <?php if ($hasDiscount): ?>
            <span class="bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow">
                -<?= discountPercent($product['price'], $product['discount_price']) ?>%
            </span>
            <?php endif; ?>
            
            <?php if ($isOutOfStock): ?>
            <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">Hết hàng</span>
            <?php endif; ?>
        </div>

        <!-- Quick Actions (hover) -->
        <div class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
            <?php if (Session::isLoggedIn()): ?>
            <button onclick="event.preventDefault(); addToWishlist(<?= $product['id'] ?>)" 
                    class="w-9 h-9 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gradient-to-r hover:from-red-500 hover:to-pink-500 hover:text-white transition-all">
                <i class="far fa-heart"></i>
            </button>
            <?php endif; ?>
        </div>
        
        <!-- View button on hover -->
        <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
            <span class="block w-full py-2 bg-white/90 backdrop-blur text-center text-sm font-medium rounded-lg shadow">
                Xem chi tiết
            </span>
        </div>
    </div>

    <!-- Info -->
    <div class="p-3 md:p-4">
        <!-- Brand -->
        <?php if (!empty($product['brand_name'])): ?>
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1 font-medium"><?= $product['brand_name'] ?></p>
        <?php endif; ?>

        <!-- Name -->
        <h3 class="font-medium text-sm md:text-base mb-2 line-clamp-2 group-hover:text-accent transition">
            <?= htmlspecialchars($product['name']) ?>
        </h3>

        <!-- Price -->
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-accent font-bold text-base"><?= formatMoney($finalPrice) ?></span>
            <?php if ($hasDiscount): ?>
            <span class="text-gray-400 text-sm line-through"><?= formatMoney($product['price']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</a>
