-- =============================================
-- DỮ LIỆU MẪU CHO SNEAKER STORE
-- Chạy file này trong MySQL Workbench
-- =============================================

USE shop_giay_db;

-- 1. THÊM DANH MỤC
INSERT INTO categories (name, slug, status) VALUES 
('Sneaker', 'sneaker', 1),
('Giày chạy bộ', 'giay-chay-bo', 1),
('Giày bóng rổ', 'giay-bong-ro', 1),
('Giày lifestyle', 'giay-lifestyle', 1);

-- 2. THÊM THƯƠNG HIỆU
INSERT INTO brands (name, slug, logo) VALUES 
('Nike', 'nike', NULL),
('Adidas', 'adidas', NULL),
('Puma', 'puma', NULL),
('New Balance', 'new-balance', NULL),
('Converse', 'converse', NULL);

-- 3. THÊM TAGS
INSERT INTO tags (name, type) VALUES 
('Đi tiệc', 'Purpose'),
('Đi làm', 'Purpose'),
('Đi chơi', 'Purpose'),
('Chạy bộ', 'Purpose'),
('Chống nước', 'Feature'),
('Thoáng khí', 'Feature');

-- 4. THÊM SẢN PHẨM
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender, views) VALUES 
-- Nike
(1, 1, 'Nike Air Force 1 Low White', 'nike-air-force-1-low-white', 2800000, 2520000, 'nike-af1-white.jpg', 'Đôi giày huyền thoại Nike Air Force 1 với thiết kế cổ điển, màu trắng tinh khôi phù hợp mọi phong cách.', 'Unisex', 1250),
(1, 1, 'Nike Air Max 90', 'nike-air-max-90', 3500000, 0, 'nike-airmax90.jpg', 'Nike Air Max 90 với công nghệ đệm Air Max mang đến sự thoải mái tối đa.', 'Male', 890),
(1, 1, 'Nike Dunk Low Panda', 'nike-dunk-low-panda', 3200000, 2880000, 'nike-dunk-panda.jpg', 'Nike Dunk Low phối màu Panda đen trắng cực hot, phù hợp mọi outfit.', 'Unisex', 2100),
(3, 1, 'Nike Jordan 1 Retro High', 'nike-jordan-1-retro-high', 4500000, 0, 'nike-jordan1.jpg', 'Air Jordan 1 Retro High - Đôi giày bóng rổ huyền thoại của Michael Jordan.', 'Male', 1560),
(2, 1, 'Nike Pegasus 40', 'nike-pegasus-40', 3200000, 2720000, 'nike-pegasus40.jpg', 'Giày chạy bộ Nike Pegasus 40 với đệm Zoom Air êm ái.', 'Unisex', 670),

-- Adidas
(1, 2, 'Adidas Stan Smith', 'adidas-stan-smith', 2500000, 0, 'adidas-stansmith.jpg', 'Adidas Stan Smith - Đôi giày tennis cổ điển với thiết kế tối giản.', 'Unisex', 980),
(1, 2, 'Adidas Superstar', 'adidas-superstar', 2600000, 2340000, 'adidas-superstar.jpg', 'Adidas Superstar với mũi giày shell-toe đặc trưng.', 'Unisex', 1120),
(2, 2, 'Adidas Ultraboost 23', 'adidas-ultraboost-23', 4200000, 3780000, 'adidas-ultraboost.jpg', 'Adidas Ultraboost 23 với công nghệ Boost mang đến cảm giác êm ái nhất.', 'Unisex', 750),
(4, 2, 'Adidas Samba OG', 'adidas-samba-og', 2800000, 0, 'adidas-samba.jpg', 'Adidas Samba OG - Đôi giày indoor football trở thành icon thời trang.', 'Unisex', 1890),

-- Puma  
(1, 3, 'Puma Suede Classic', 'puma-suede-classic', 2200000, 1980000, 'puma-suede.jpg', 'Puma Suede Classic với chất liệu da lộn cao cấp.', 'Unisex', 560),
(2, 3, 'Puma RS-X', 'puma-rs-x', 2900000, 0, 'puma-rsx.jpg', 'Puma RS-X với thiết kế chunky đậm chất retro.', 'Unisex', 420),

-- New Balance
(4, 4, 'New Balance 550', 'new-balance-550', 3200000, 2880000, 'nb-550.jpg', 'New Balance 550 - Đôi giày basketball retro đang hot nhất hiện nay.', 'Unisex', 1650),
(2, 4, 'New Balance 990v5', 'new-balance-990v5', 4800000, 0, 'nb-990v5.jpg', 'New Balance 990v5 Made in USA - Đỉnh cao của sự thoải mái.', 'Male', 380),

-- Converse
(1, 5, 'Converse Chuck Taylor All Star', 'converse-chuck-taylor', 1500000, 1350000, 'converse-chuck.jpg', 'Converse Chuck Taylor All Star - Đôi giày canvas huyền thoại.', 'Unisex', 2300),
(1, 5, 'Converse Chuck 70', 'converse-chuck-70', 1900000, 0, 'converse-chuck70.jpg', 'Converse Chuck 70 với chất lượng cao cấp hơn phiên bản thường.', 'Unisex', 890);


-- 5. THÊM BIẾN THỂ (SIZE/MÀU) CHO SẢN PHẨM
-- Nike Air Force 1 (ID: 1)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(1, '38', 'Trắng', 10), (1, '39', 'Trắng', 15), (1, '40', 'Trắng', 20),
(1, '41', 'Trắng', 18), (1, '42', 'Trắng', 12), (1, '43', 'Trắng', 8);

-- Nike Air Max 90 (ID: 2)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(2, '40', 'Đen', 10), (2, '41', 'Đen', 12), (2, '42', 'Đen', 15),
(2, '40', 'Trắng', 8), (2, '41', 'Trắng', 10), (2, '42', 'Trắng', 12);

-- Nike Dunk Low Panda (ID: 3)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(3, '38', 'Đen/Trắng', 5), (3, '39', 'Đen/Trắng', 8), (3, '40', 'Đen/Trắng', 12),
(3, '41', 'Đen/Trắng', 15), (3, '42', 'Đen/Trắng', 10), (3, '43', 'Đen/Trắng', 6);

-- Nike Jordan 1 (ID: 4)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(4, '40', 'Đỏ/Đen', 5), (4, '41', 'Đỏ/Đen', 8), (4, '42', 'Đỏ/Đen', 10),
(4, '43', 'Đỏ/Đen', 6), (4, '44', 'Đỏ/Đen', 4);

-- Nike Pegasus 40 (ID: 5)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(5, '39', 'Xanh', 10), (5, '40', 'Xanh', 15), (5, '41', 'Xanh', 12),
(5, '42', 'Xanh', 10), (5, '43', 'Xanh', 8);

-- Adidas Stan Smith (ID: 6)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(6, '38', 'Trắng/Xanh', 12), (6, '39', 'Trắng/Xanh', 15), (6, '40', 'Trắng/Xanh', 18),
(6, '41', 'Trắng/Xanh', 14), (6, '42', 'Trắng/Xanh', 10);

-- Adidas Superstar (ID: 7)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(7, '38', 'Trắng/Đen', 10), (7, '39', 'Trắng/Đen', 12), (7, '40', 'Trắng/Đen', 15),
(7, '41', 'Trắng/Đen', 12), (7, '42', 'Trắng/Đen', 8);

-- Adidas Ultraboost (ID: 8)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(8, '40', 'Đen', 8), (8, '41', 'Đen', 10), (8, '42', 'Đen', 12),
(8, '43', 'Đen', 8), (8, '44', 'Đen', 5);

-- Adidas Samba (ID: 9)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(9, '38', 'Đen', 10), (9, '39', 'Đen', 15), (9, '40', 'Đen', 18),
(9, '41', 'Đen', 14), (9, '42', 'Đen', 10), (9, '43', 'Đen', 6);

-- Puma Suede (ID: 10)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(10, '39', 'Đen', 8), (10, '40', 'Đen', 10), (10, '41', 'Đen', 12),
(10, '42', 'Đen', 8);

-- Puma RS-X (ID: 11)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(11, '40', 'Trắng/Xanh', 6), (11, '41', 'Trắng/Xanh', 8), (11, '42', 'Trắng/Xanh', 10),
(11, '43', 'Trắng/Xanh', 6);

-- New Balance 550 (ID: 12)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(12, '39', 'Trắng/Xanh', 10), (12, '40', 'Trắng/Xanh', 15), (12, '41', 'Trắng/Xanh', 12),
(12, '42', 'Trắng/Xanh', 10), (12, '43', 'Trắng/Xanh', 6);

-- New Balance 990v5 (ID: 13)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(13, '40', 'Xám', 5), (13, '41', 'Xám', 8), (13, '42', 'Xám', 10),
(13, '43', 'Xám', 6), (13, '44', 'Xám', 4);

-- Converse Chuck Taylor (ID: 14)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(14, '36', 'Đen', 15), (14, '37', 'Đen', 18), (14, '38', 'Đen', 20),
(14, '39', 'Đen', 22), (14, '40', 'Đen', 18), (14, '41', 'Đen', 15),
(14, '36', 'Trắng', 12), (14, '37', 'Trắng', 15), (14, '38', 'Trắng', 18),
(14, '39', 'Trắng', 20), (14, '40', 'Trắng', 16), (14, '41', 'Trắng', 12);

-- Converse Chuck 70 (ID: 15)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES 
(15, '38', 'Đen', 10), (15, '39', 'Đen', 12), (15, '40', 'Đen', 15),
(15, '41', 'Đen', 12), (15, '42', 'Đen', 8);

-- 6. THÊM MÃ GIẢM GIÁ
INSERT INTO coupons (code, discount_type, discount_value, min_order_value, start_date, end_date, usage_limit, status) VALUES 
('WELCOME10', 'percent', 10, 500000, '2024-01-01', '2025-12-31', 1000, 1),
('SALE50K', 'fixed', 50000, 300000, '2024-01-01', '2025-12-31', 500, 1),
('FREESHIP', 'fixed', 30000, 200000, '2024-01-01', '2025-12-31', 1000, 1);

-- 7. THÊM TÀI KHOẢN ADMIN
INSERT INTO tblUser (username, password, email, fullname, role, status, created_at) VALUES 
('admin', '$2y$10$Kp2FhGG65COc/V850NJN1u5.TLyvEQiLmxDwKogvR4Zw0JbKhGaca', 'admin@sneaker.com', 'Quản Trị Viên', 2, 1, NOW());
-- Mật khẩu: 123456

SELECT 'Đã thêm dữ liệu mẫu thành công!' AS Result;
