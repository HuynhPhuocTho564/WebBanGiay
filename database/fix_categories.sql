-- ==========================================================
-- GIỮ LẠI 7 DANH MỤC GIÀY PHỔ BIẾN
-- ==========================================================
-- Giữ lại: Sneaker, Giày Chạy Bộ, Giày Thể Thao, Giày Cao Gót, Sandal, Boot, Giày Lười
-- Xóa: Giày Búp Bê, Giày bóng rổ, Giày lifestyle, và các danh mục trùng/không cần

SET SQL_SAFE_UPDATES = 0;

-- Bước 1: Xem danh mục hiện có
SELECT id, name, slug FROM categories ORDER BY name;

-- Bước 2: Xóa danh mục trùng (giữ ID nhỏ nhất)
UPDATE products p
JOIN categories c1 ON p.category_id = c1.id
JOIN categories c2 ON c1.name = c2.name AND c2.id < c1.id
SET p.category_id = c2.id;

DELETE c1 FROM categories c1
INNER JOIN categories c2 
WHERE c1.id > c2.id AND c1.name = c2.name;

-- Bước 3: Danh sách 7 danh mục giữ lại (phổ biến cho cả nam và nữ)
-- 1. Sneaker - Giày sneaker thời trang
-- 2. Giày Chạy Bộ - Running shoes
-- 3. Giày Thể Thao - Sport shoes
-- 4. Giày Cao Gót - High heels (nữ)
-- 5. Sandal - Dép sandal
-- 6. Boot - Giày boot
-- 7. Giày Lười - Slip-on/Loafer

-- Bước 4: Xóa variants của sản phẩm thuộc danh mục sẽ bị xóa
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
JOIN categories c ON p.category_id = c.id
WHERE c.name NOT IN ('Sneaker', 'Giày Chạy Bộ', 'Giày Thể Thao', 'Giày Cao Gót', 'Sandal', 'Boot', 'Giày Lười')
  AND c.slug NOT IN ('sneaker', 'giay-chay-bo', 'giay-the-thao', 'giay-cao-got', 'sandal', 'boot', 'giay-luoi');

-- Bước 5: Xóa sản phẩm thuộc danh mục sẽ bị xóa
DELETE p FROM products p
JOIN categories c ON p.category_id = c.id
WHERE c.name NOT IN ('Sneaker', 'Giày Chạy Bộ', 'Giày Thể Thao', 'Giày Cao Gót', 'Sandal', 'Boot', 'Giày Lười')
  AND c.slug NOT IN ('sneaker', 'giay-chay-bo', 'giay-the-thao', 'giay-cao-got', 'sandal', 'boot', 'giay-luoi');

-- Bước 6: Xóa các danh mục không cần
DELETE FROM categories 
WHERE name NOT IN ('Sneaker', 'Giày Chạy Bộ', 'Giày Thể Thao', 'Giày Cao Gót', 'Sandal', 'Boot', 'Giày Lười')
  AND slug NOT IN ('sneaker', 'giay-chay-bo', 'giay-the-thao', 'giay-cao-got', 'sandal', 'boot', 'giay-luoi');

-- Bước 7: Xóa sản phẩm không có danh mục
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.category_id IS NULL;

DELETE FROM products WHERE category_id IS NULL;

-- Bước 8: Xóa variants mồ côi
DELETE pv FROM product_variants pv
LEFT JOIN products p ON pv.product_id = p.id
WHERE p.id IS NULL;

SET SQL_SAFE_UPDATES = 1;

-- Kiểm tra kết quả
SELECT id, name, slug FROM categories ORDER BY name;
SELECT COUNT(*) as total_products FROM products;
