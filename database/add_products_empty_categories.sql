-- ==========================================================
-- THÊM SẢN PHẨM VÀO CÁC DANH MỤC TRỐNG
-- Boot, Giày Cao Gót, Giày Thể Thao, Sandal
-- ==========================================================

USE shop_giay_db;

-- 1. BOOT - 2 sản phẩm
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender) VALUES
((SELECT id FROM categories WHERE slug = 'boot'), (SELECT id FROM brands WHERE slug = 'nike'), 'Nike Air Force 1 Boot', 'nike-air-force-1-boot', 4200000, 3800000, 'https://images.unsplash.com/photo-1520639888713-7851133b1ed0?w=500', 'Boot Nike Air Force 1 cổ cao, chống nước, phù hợp mùa đông.', 'Male'),
((SELECT id FROM categories WHERE slug = 'boot'), (SELECT id FROM brands WHERE slug = 'adidas'), 'Adidas Terrex Free Hiker', 'adidas-terrex-free-hiker', 4500000, 4000000, 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=500', 'Boot leo núi Adidas Terrex với công nghệ Boost, bám đường tốt.', 'Unisex');

-- 2. GIÀY CAO GÓT - 2 sản phẩm
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender) VALUES
((SELECT id FROM categories WHERE slug = 'giay-cao-got'), (SELECT id FROM brands WHERE slug = 'fila'), 'Fila Disruptor Wedge', 'fila-disruptor-wedge', 2800000, 2500000, 'https://images.unsplash.com/photo-1596703263926-eb0762ee17e4?w=500', 'Giày Fila Disruptor đế xuồng, tăng chiều cao tự nhiên.', 'Female'),
((SELECT id FROM categories WHERE slug = 'giay-cao-got'), (SELECT id FROM brands WHERE slug = 'puma'), 'Puma Mayze Platform', 'puma-mayze-platform', 3200000, 2900000, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500', 'Giày Puma Mayze đế platform cao 4cm, phong cách retro.', 'Female');

-- 3. GIÀY THỂ THAO - 2 sản phẩm (bổ sung thêm)
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender) VALUES
((SELECT id FROM categories WHERE slug = 'giay-the-thao'), (SELECT id FROM brands WHERE slug = 'nike'), 'Nike Revolution 6', 'nike-revolution-6', 1800000, 1500000, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=500', 'Giày chạy bộ Nike Revolution 6, nhẹ và thoáng khí.', 'Male'),
((SELECT id FROM categories WHERE slug = 'giay-the-thao'), (SELECT id FROM brands WHERE slug = 'new-balance'), 'New Balance 990v5', 'new-balance-990v5', 4800000, 4500000, 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=500', 'New Balance 990v5 Made in USA, đệm ENCAP cao cấp.', 'Unisex');

-- 4. SANDAL - 1 sản phẩm (bổ sung thêm)
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender) VALUES
((SELECT id FROM categories WHERE slug = 'sandal'), (SELECT id FROM brands WHERE slug = 'adidas'), 'Adidas Adilette Comfort', 'adidas-adilette-comfort', 950000, 800000, 'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=500', 'Dép Adidas Adilette êm ái với đệm Cloudfoam.', 'Unisex');

-- Thêm variants cho các sản phẩm mới (màu đơn giản)

-- Nike Air Force 1 Boot - Màu Đen
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Đen', 10
FROM products p
CROSS JOIN (SELECT '40' as size UNION SELECT '41' UNION SELECT '42' UNION SELECT '43') s
WHERE p.slug = 'nike-air-force-1-boot';

-- Adidas Terrex Free Hiker - Màu Xám
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Xám', 8
FROM products p
CROSS JOIN (SELECT '40' as size UNION SELECT '41' UNION SELECT '42' UNION SELECT '43') s
WHERE p.slug = 'adidas-terrex-free-hiker';

-- Fila Disruptor Wedge - Màu Trắng
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Trắng', 12
FROM products p
CROSS JOIN (SELECT '36' as size UNION SELECT '37' UNION SELECT '38' UNION SELECT '39') s
WHERE p.slug = 'fila-disruptor-wedge';

-- Puma Mayze Platform - Màu Đen
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Đen', 10
FROM products p
CROSS JOIN (SELECT '36' as size UNION SELECT '37' UNION SELECT '38' UNION SELECT '39') s
WHERE p.slug = 'puma-mayze-platform';

-- Nike Revolution 6 - Màu Đen
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Đen', 15
FROM products p
CROSS JOIN (SELECT '40' as size UNION SELECT '41' UNION SELECT '42' UNION SELECT '43') s
WHERE p.slug = 'nike-revolution-6';

-- New Balance 990v5 - Màu Xám
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Xám', 6
FROM products p
CROSS JOIN (SELECT '40' as size UNION SELECT '41' UNION SELECT '42' UNION SELECT '43') s
WHERE p.slug = 'new-balance-990v5';

-- Adidas Adilette Comfort - Màu Đen
INSERT INTO product_variants (product_id, size, color, stock_quantity)
SELECT p.id, s.size, 'Đen', 20
FROM products p
CROSS JOIN (SELECT '38' as size UNION SELECT '39' UNION SELECT '40' UNION SELECT '41' UNION SELECT '42') s
WHERE p.slug = 'adidas-adilette-comfort';

-- Thông báo hoàn tất
SELECT 'Đã thêm 7 sản phẩm vào các danh mục trống!' AS Message;
SELECT c.name as 'Danh mục', COUNT(p.id) as 'Số sản phẩm' 
FROM categories c 
LEFT JOIN products p ON c.id = p.category_id 
GROUP BY c.id, c.name;
