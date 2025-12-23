-- ==========================================================
-- XÓA SẢN PHẨM KHÔNG THUỘC 7 DANH MỤC VÀ 7 THƯƠNG HIỆU
-- ==========================================================
-- 7 Danh mục: Boot, Giày Cao Gót, Giày chạy bộ, Giày Lười, Giày Thể Thao, Sandal, Sneaker
-- 7 Thương hiệu: Adidas, Converse, Fila, New Balance, Nike, Puma, Vans

SET SQL_SAFE_UPDATES = 0;

-- Bước 1: Xóa variants của sản phẩm không thuộc 7 thương hiệu
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.brand_id NOT IN (SELECT id FROM brands WHERE name IN ('Adidas', 'Converse', 'Fila', 'New Balance', 'Nike', 'Puma', 'Vans'));

-- Bước 2: Xóa sản phẩm không thuộc 7 thương hiệu
DELETE FROM products 
WHERE brand_id NOT IN (SELECT id FROM brands WHERE name IN ('Adidas', 'Converse', 'Fila', 'New Balance', 'Nike', 'Puma', 'Vans'));

-- Bước 3: Xóa variants của sản phẩm không thuộc 7 danh mục
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.category_id NOT IN (SELECT id FROM categories WHERE name IN ('Boot', 'Giày Cao Gót', 'Giày Chạy Bộ', 'Giày chạy bộ', 'Giày Lười', 'Giày Thể Thao', 'Sandal', 'Sneaker'));

-- Bước 4: Xóa sản phẩm không thuộc 7 danh mục
DELETE FROM products 
WHERE category_id NOT IN (SELECT id FROM categories WHERE name IN ('Boot', 'Giày Cao Gót', 'Giày Chạy Bộ', 'Giày chạy bộ', 'Giày Lười', 'Giày Thể Thao', 'Sandal', 'Sneaker'));

-- Bước 5: Xóa sản phẩm không có danh mục hoặc thương hiệu
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.category_id IS NULL OR p.brand_id IS NULL;

DELETE FROM products WHERE category_id IS NULL OR brand_id IS NULL;

-- Bước 6: Xóa thương hiệu không nằm trong 7 thương hiệu
DELETE FROM brands WHERE name NOT IN ('Adidas', 'Converse', 'Fila', 'New Balance', 'Nike', 'Puma', 'Vans');

-- Bước 7: Xóa danh mục không nằm trong 7 danh mục
DELETE FROM categories WHERE name NOT IN ('Boot', 'Giày Cao Gót', 'Giày Chạy Bộ', 'Giày chạy bộ', 'Giày Lười', 'Giày Thể Thao', 'Sandal', 'Sneaker');

-- Bước 8: Xóa variants mồ côi
DELETE pv FROM product_variants pv
LEFT JOIN products p ON pv.product_id = p.id
WHERE p.id IS NULL;

SET SQL_SAFE_UPDATES = 1;

-- Kiểm tra kết quả
SELECT 'Danh mục' as type, COUNT(*) as count FROM categories
UNION ALL
SELECT 'Thương hiệu', COUNT(*) FROM brands
UNION ALL
SELECT 'Sản phẩm', COUNT(*) FROM products;

-- Xem chi tiết
SELECT id, name FROM categories ORDER BY name;
SELECT id, name FROM brands ORDER BY name;
