-- ==========================================================
-- GIỮ LẠI 7 THƯƠNG HIỆU, XÓA CÒN LẠI VÀ SẢN PHẨM LIÊN QUAN
-- ==========================================================

SET SQL_SAFE_UPDATES = 0;

-- Bước 1: Xóa thương hiệu trùng trước (giữ ID nhỏ nhất)
UPDATE products p
JOIN brands b1 ON p.brand_id = b1.id
JOIN brands b2 ON b1.name = b2.name AND b2.id < b1.id
SET p.brand_id = b2.id;

DELETE b1 FROM brands b1
INNER JOIN brands b2 
WHERE b1.id > b2.id AND b1.name = b2.name;

-- Bước 2: Xóa variants của sản phẩm thuộc thương hiệu sẽ bị xóa (Asics, Reebok, Skechers)
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
JOIN brands b ON p.brand_id = b.id
WHERE b.name IN ('Asics', 'Reebok', 'Skechers');

-- Bước 3: Xóa sản phẩm thuộc thương hiệu sẽ bị xóa
DELETE p FROM products p
JOIN brands b ON p.brand_id = b.id
WHERE b.name IN ('Asics', 'Reebok', 'Skechers');

-- Bước 4: Xóa 3 thương hiệu (giữ lại 7: Nike, Adidas, Puma, Converse, Vans, New Balance, Fila)
DELETE FROM brands WHERE name IN ('Asics', 'Reebok', 'Skechers');

-- Bước 5: Xóa sản phẩm không có thương hiệu (brand_id = NULL)
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.brand_id IS NULL;

DELETE FROM products WHERE brand_id IS NULL;

-- Bước 6: Xóa variants mồ côi (nếu có)
DELETE pv FROM product_variants pv
LEFT JOIN products p ON pv.product_id = p.id
WHERE p.id IS NULL;

SET SQL_SAFE_UPDATES = 1;

-- Kiểm tra kết quả
SELECT id, name FROM brands ORDER BY name;
SELECT COUNT(*) as total_products FROM products;
