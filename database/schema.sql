-- =============================================
-- SNEAKER STORE - DATABASE SCHEMA & SAMPLE DATA
-- File gom tất cả SQL scripts
-- Chạy file này trong MySQL Workbench
-- =============================================

USE shop_giay_db;

SET SQL_SAFE_UPDATES = 0;

-- =============================================
-- PHẦN 1: CẬP NHẬT CẤU TRÚC BẢNG
-- =============================================

-- Thêm cột status vào bảng brands (nếu chưa có)
ALTER TABLE brands ADD COLUMN IF NOT EXISTS status TINYINT DEFAULT 1;

-- Thêm cột cancel_reason vào bảng orders (nếu chưa có)
ALTER TABLE orders ADD COLUMN IF NOT EXISTS cancel_reason TEXT DEFAULT NULL COMMENT 'Lý do hủy đơn hàng' AFTER status;

-- =============================================
-- PHẦN 2: XÓA DỮ LIỆU TRÙNG LẶP
-- =============================================

-- Xóa danh mục trùng (giữ lại ID nhỏ nhất)
DELETE c1 FROM categories c1
INNER JOIN categories c2 
WHERE c1.id > c2.id AND c1.name = c2.name;

-- Xóa thương hiệu trùng (giữ lại ID nhỏ nhất)
DELETE b1 FROM brands b1
INNER JOIN brands b2 
WHERE b1.id > b2.id AND b1.name = b2.name;

-- =============================================
-- PHẦN 3: THÊM 7 DANH MỤC
-- =============================================
INSERT IGNORE INTO categories (name, slug, status) VALUES
('Sneaker', 'sneaker', 1),
('Giày Chạy Bộ', 'giay-chay-bo', 1),
('Giày Thể Thao', 'giay-the-thao', 1),
('Giày Cao Gót', 'giay-cao-got', 1),
('Sandal', 'sandal', 1),
('Boot', 'boot', 1),
('Giày Lười', 'giay-luoi', 1);

-- =============================================
-- PHẦN 4: THÊM 7 THƯƠNG HIỆU
-- =============================================
INSERT IGNORE INTO brands (name, slug, logo, status) VALUES
('Nike', 'nike', NULL, 1),
('Adidas', 'adidas', NULL, 1),
('Puma', 'puma', NULL, 1),
('New Balance', 'new-balance', NULL, 1),
('Converse', 'converse', NULL, 1),
('Vans', 'vans', NULL, 1),
('Fila', 'fila', NULL, 1);

-- =============================================
-- PHẦN 5: XÓA DỮ LIỆU KHÔNG CẦN THIẾT
-- =============================================

-- Xóa sản phẩm không thuộc 7 thương hiệu
DELETE pv FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.brand_id NOT IN (SELECT id FROM brands WHERE name IN ('Nike', 'Adidas', 'Puma', 'New Balance', 'Converse', 'Vans', 'Fila'));

DELETE FROM products 
WHERE brand_id NOT IN (SELECT id FROM brands WHERE name IN ('Nike', 'Adidas', 'Puma', 'New Balance', 'Converse', 'Vans', 'Fila'));

-- Xóa thương hiệu không cần
DELETE FROM brands WHERE name NOT IN ('Nike', 'Adidas', 'Puma', 'New Balance', 'Converse', 'Vans', 'Fila');

-- Xóa danh mục không cần
DELETE FROM categories WHERE slug NOT IN ('sneaker', 'giay-chay-bo', 'giay-the-thao', 'giay-cao-got', 'sandal', 'boot', 'giay-luoi');

-- Xóa variants mồ côi
DELETE pv FROM product_variants pv
LEFT JOIN products p ON pv.product_id = p.id
WHERE p.id IS NULL;

-- =============================================
-- PHẦN 6: SỬA LỖI AVATAR
-- =============================================
UPDATE tblUser SET avatar = NULL WHERE avatar IN ('Avatar', 'avatar', '');

-- =============================================
-- PHẦN 7: THÊM MÃ GIẢM GIÁ MẪU
-- =============================================
INSERT IGNORE INTO coupons (code, discount_type, discount_value, min_order_value, start_date, end_date, usage_limit, status) VALUES 
('WELCOME10', 'percent', 10, 500000, '2024-01-01', '2025-12-31', 1000, 1),
('SALE50K', 'fixed', 50000, 300000, '2024-01-01', '2025-12-31', 500, 1),
('FREESHIP', 'fixed', 30000, 200000, '2024-01-01', '2025-12-31', 1000, 1);

-- =============================================
-- PHẦN 8: THÊM TÀI KHOẢN ADMIN & STAFF
-- =============================================
-- Mật khẩu: 123456
INSERT IGNORE INTO tblUser (username, password, email, fullname, role, status, created_at) VALUES 
('admin', '$2y$10$Kp2FhGG65COc/V850NJN1u5.TLyvEQiLmxDwKogvR4Zw0JbKhGaca', 'admin@sneaker.com', 'Quản Trị Viên', 2, 1, NOW()),
('staff', '$2y$10$Kp2FhGG65COc/V850NJN1u5.TLyvEQiLmxDwKogvR4Zw0JbKhGaca', 'staff@sneaker.com', 'Nhân Viên', 1, 1, NOW());

SET SQL_SAFE_UPDATES = 1;

SELECT 'Hoàn tất cập nhật database!' AS Result;
