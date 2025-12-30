-- ==========================================================
-- WEBSITE BÁN GIÀY - SNEAKER STORE
-- File: schema.sql
-- Mô tả: Tạo database, tables và dữ liệu mẫu
-- ==========================================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS shop_giay_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop_giay_db;

-- ==========================================================
-- PHẦN 1: TẠO CÁC BẢNG
-- ==========================================================

-- 1. Bảng người dùng
CREATE TABLE IF NOT EXISTS tblUser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fullname VARCHAR(100) NOT NULL,
    google_id VARCHAR(255) DEFAULT NULL,
    facebook_id VARCHAR(255) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    dob DATE DEFAULT NULL,
    phone_number VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    role TINYINT DEFAULT 0 COMMENT '0: Khách hàng, 1: Nhân viên, 2: Admin',
    status TINYINT DEFAULT 1 COMMENT '1: Hoạt động, 0: Bị khóa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Bảng danh mục
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    status TINYINT DEFAULT 1
);

-- 3. Bảng thương hiệu
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) DEFAULT NULL
);

-- 4. Bảng sản phẩm
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    brand_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(12, 2) NOT NULL,
    discount_price DECIMAL(12, 2) DEFAULT 0,
    thumbnail VARCHAR(255) DEFAULT NULL,
    description TEXT,
    video_url VARCHAR(255) DEFAULT NULL,
    gender ENUM('Male', 'Female', 'Unisex') DEFAULT 'Unisex',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);

-- 5. Bảng biến thể sản phẩm (size, màu, tồn kho)
CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(10) NOT NULL,
    color VARCHAR(50) NOT NULL,
    stock_quantity INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 6. Bảng gallery ảnh sản phẩm
CREATE TABLE IF NOT EXISTS galleries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 7. Bảng mã giảm giá
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('percent', 'fixed') DEFAULT 'percent',
    discount_value DECIMAL(12, 2) NOT NULL,
    min_order_value DECIMAL(12, 2) DEFAULT 0,
    start_date DATETIME,
    end_date DATETIME,
    usage_limit INT DEFAULT 100,
    used_count INT DEFAULT 0,
    status TINYINT DEFAULT 1
);

-- 8. Bảng đơn hàng
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone_number VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    note TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled', 'returning', 'returned') DEFAULT 'pending',
    total_money DECIMAL(12, 2),
    payment_method VARCHAR(50) DEFAULT 'COD',
    coupon_id INT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE SET NULL,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
);

-- 9. Bảng chi tiết đơn hàng
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_variant_id INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    quantity INT NOT NULL,
    total_item_price DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id)
);

-- 10. Bảng yêu cầu đổi trả
CREATE TABLE IF NOT EXISTS return_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    images TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    admin_reply TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE
);

-- 11. Bảng đánh giá sản phẩm
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating TINYINT DEFAULT 5,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- 12. Bảng yêu thích
CREATE TABLE IF NOT EXISTS wishlists (
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ==========================================================
-- PHẦN 2: DỮ LIỆU MẪU
-- ==========================================================

-- Tắt safe update mode
SET SQL_SAFE_UPDATES = 0;

-- 1. Thêm Admin (password: 123456)
INSERT INTO tblUser (username, password, email, fullname, role, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sneakerstore.com', 'Administrator', 2, 1),
('staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff@sneakerstore.com', 'Nhân viên', 1, 1),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user1@gmail.com', 'Nguyễn Văn A', 0, 1);

-- 2. Thêm 7 danh mục
INSERT INTO categories (name, slug, status) VALUES
('Sneaker', 'sneaker', 1),
('Giày Chạy Bộ', 'giay-chay-bo', 1),
('Giày Thể Thao', 'giay-the-thao', 1),
('Giày Cao Gót', 'giay-cao-got', 1),
('Sandal', 'sandal', 1),
('Boot', 'boot', 1),
('Giày Lười', 'giay-luoi', 1);

-- 3. Thêm 7 thương hiệu
INSERT INTO brands (name, slug, logo) VALUES
('Nike', 'nike', 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg'),
('Adidas', 'adidas', 'https://upload.wikimedia.org/wikipedia/commons/2/20/Adidas_Logo.svg'),
('Puma', 'puma', 'https://upload.wikimedia.org/wikipedia/en/d/da/Puma_complete_logo.svg'),
('New Balance', 'new-balance', 'https://upload.wikimedia.org/wikipedia/commons/e/ea/New_Balance_logo.svg'),
('Converse', 'converse', 'https://upload.wikimedia.org/wikipedia/commons/3/30/Converse_logo.svg'),
('Vans', 'vans', 'https://upload.wikimedia.org/wikipedia/commons/9/91/Vans-logo.svg'),
('Fila', 'fila', 'https://upload.wikimedia.org/wikipedia/commons/7/7f/Fila_logo.svg');

-- 4. Thêm sản phẩm Nam
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender) VALUES
-- Nike Nam
(1, 1, 'Nike Air Force 1 Low White', 'nike-air-force-1-low-white', 2800000, 2500000, 'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=500', 'Giày Nike Air Force 1 phiên bản trắng cổ điển, thiết kế đơn giản nhưng sang trọng.', 'Male'),
(1, 1, 'Nike Air Max 90', 'nike-air-max-90', 3500000, 3200000, 'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=500', 'Nike Air Max 90 với công nghệ đệm Air Max mang lại sự thoải mái tối đa.', 'Male'),
(2, 1, 'Nike Pegasus 40', 'nike-pegasus-40', 3200000, 2900000, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500', 'Giày chạy bộ Nike Pegasus 40 với đệm Zoom Air êm ái.', 'Male'),

-- Adidas Nam
(1, 2, 'Adidas Superstar', 'adidas-superstar', 2500000, 2200000, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=500', 'Adidas Superstar với thiết kế shell toe đặc trưng.', 'Male'),
(3, 2, 'Adidas Ultraboost 23', 'adidas-ultraboost-23', 4500000, 4000000, 'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=500', 'Adidas Ultraboost 23 với công nghệ Boost mang lại năng lượng hoàn hảo.', 'Male'),

-- Puma Nam
(3, 3, 'Puma RS-X', 'puma-rs-x', 2800000, 2500000, 'https://images.unsplash.com/photo-1608379743498-63d4e2f1e3c0?w=500', 'Puma RS-X với thiết kế chunky retro đầy cá tính.', 'Male'),

-- New Balance Nam
(1, 4, 'New Balance 574', 'new-balance-574', 2600000, 2300000, 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=500', 'New Balance 574 - mẫu giày iconic với sự thoải mái vượt trội.', 'Male'),
(2, 4, 'New Balance Fresh Foam 1080', 'new-balance-fresh-foam-1080', 4200000, 3800000, 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=500', 'Giày chạy bộ cao cấp với đệm Fresh Foam.', 'Male'),

-- Converse Nam
(1, 5, 'Converse Chuck Taylor All Star', 'converse-chuck-taylor-all-star', 1500000, 1300000, 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=500', 'Converse Chuck Taylor - biểu tượng thời trang từ năm 1917.', 'Male'),

-- Vans Nam
(1, 6, 'Vans Old Skool', 'vans-old-skool', 1800000, 1600000, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500', 'Vans Old Skool với sọc jazz stripe đặc trưng.', 'Male'),

-- Fila Nam
(3, 7, 'Fila Disruptor 2', 'fila-disruptor-2', 2200000, 1900000, 'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=500', 'Fila Disruptor 2 với đế chunky thời thượng.', 'Male');

-- 5. Thêm sản phẩm Nữ
INSERT INTO products (category_id, brand_id, name, slug, price, discount_price, thumbnail, description, gender) VALUES
-- Nike Nữ
(1, 1, 'Nike Air Force 1 Shadow', 'nike-air-force-1-shadow', 3200000, 2900000, 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=500', 'Nike Air Force 1 Shadow với thiết kế layer độc đáo dành cho nữ.', 'Female'),
(4, 1, 'Nike Court Legacy Lift', 'nike-court-legacy-lift', 2800000, 2500000, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=500', 'Nike Court Legacy Lift với đế platform thanh lịch.', 'Female'),

-- Adidas Nữ
(1, 2, 'Adidas Stan Smith', 'adidas-stan-smith', 2400000, 2100000, 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=500', 'Adidas Stan Smith - thiết kế tối giản, thanh lịch.', 'Female'),
(3, 2, 'Adidas NMD R1', 'adidas-nmd-r1', 3800000, 3400000, 'https://images.unsplash.com/photo-1520256862855-398228c41684?w=500', 'Adidas NMD R1 với công nghệ Boost và thiết kế hiện đại.', 'Female'),

-- Puma Nữ
(3, 3, 'Puma Cali Star', 'puma-cali-star', 2500000, 2200000, 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500', 'Puma Cali Star với đế platform nữ tính.', 'Female'),

-- New Balance Nữ
(1, 4, 'New Balance 327', 'new-balance-327', 2800000, 2500000, 'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=500', 'New Balance 327 với thiết kế retro-modern độc đáo.', 'Female'),

-- Converse Nữ
(1, 5, 'Converse Chuck 70 Hi', 'converse-chuck-70-hi', 2000000, 1800000, 'https://images.unsplash.com/photo-1494496195158-c3becb4f2475?w=500', 'Converse Chuck 70 phiên bản cao cấp với chất liệu premium.', 'Female'),

-- Vans Nữ
(1, 6, 'Vans Sk8-Hi', 'vans-sk8-hi', 2100000, 1900000, 'https://images.unsplash.com/photo-1543508282-6319a3e2621f?w=500', 'Vans Sk8-Hi cổ cao phong cách skateboard.', 'Female'),

-- Sandal Nữ
(5, 1, 'Nike Victori One Slide', 'nike-victori-one-slide', 900000, 750000, 'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=500', 'Dép Nike Victori One thoải mái cho mùa hè.', 'Female'),

-- Boot Nữ
(6, 7, 'Fila Disruptor Shearling Boot', 'fila-disruptor-shearling-boot', 3500000, 3000000, 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=500', 'Boot Fila Disruptor với lông cừu ấm áp.', 'Female'),

-- Giày Lười
(7, 6, 'Vans Slip-On', 'vans-slip-on', 1600000, 1400000, 'https://images.unsplash.com/photo-1604671801908-6f0c6a092c05?w=500', 'Vans Slip-On tiện lợi, dễ mang.', 'Unisex');


-- 6. Thêm biến thể sản phẩm (size, màu, tồn kho)
-- Sản phẩm 1: Nike Air Force 1 Low White
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(1, '39', 'Trắng', 10), (1, '40', 'Trắng', 15), (1, '41', 'Trắng', 20), (1, '42', 'Trắng', 18), (1, '43', 'Trắng', 12),
(1, '39', 'Đen', 8), (1, '40', 'Đen', 12), (1, '41', 'Đen', 15), (1, '42', 'Đen', 10), (1, '43', 'Đen', 8);

-- Sản phẩm 2: Nike Air Max 90
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(2, '39', 'Trắng/Đỏ', 8), (2, '40', 'Trắng/Đỏ', 10), (2, '41', 'Trắng/Đỏ', 12), (2, '42', 'Trắng/Đỏ', 10), (2, '43', 'Trắng/Đỏ', 6),
(2, '40', 'Đen', 10), (2, '41', 'Đen', 15), (2, '42', 'Đen', 12);

-- Sản phẩm 3: Nike Pegasus 40
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(3, '39', 'Xanh Navy', 5), (3, '40', 'Xanh Navy', 8), (3, '41', 'Xanh Navy', 10), (3, '42', 'Xanh Navy', 8), (3, '43', 'Xanh Navy', 5);

-- Sản phẩm 4: Adidas Superstar
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(4, '38', 'Trắng/Đen', 10), (4, '39', 'Trắng/Đen', 15), (4, '40', 'Trắng/Đen', 20), (4, '41', 'Trắng/Đen', 18), (4, '42', 'Trắng/Đen', 12);

-- Sản phẩm 5: Adidas Ultraboost 23
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(5, '40', 'Đen', 8), (5, '41', 'Đen', 12), (5, '42', 'Đen', 15), (5, '43', 'Đen', 10),
(5, '40', 'Trắng', 6), (5, '41', 'Trắng', 10), (5, '42', 'Trắng', 12);

-- Sản phẩm 6: Puma RS-X
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(6, '39', 'Trắng/Xanh', 8), (6, '40', 'Trắng/Xanh', 10), (6, '41', 'Trắng/Xanh', 12), (6, '42', 'Trắng/Xanh', 8);

-- Sản phẩm 7: New Balance 574
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(7, '39', 'Xám', 10), (7, '40', 'Xám', 15), (7, '41', 'Xám', 18), (7, '42', 'Xám', 12), (7, '43', 'Xám', 8);

-- Sản phẩm 8: New Balance Fresh Foam 1080
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(8, '40', 'Xanh Dương', 6), (8, '41', 'Xanh Dương', 10), (8, '42', 'Xanh Dương', 12), (8, '43', 'Xanh Dương', 8);

-- Sản phẩm 9: Converse Chuck Taylor All Star
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(9, '37', 'Đen', 15), (9, '38', 'Đen', 20), (9, '39', 'Đen', 25), (9, '40', 'Đen', 20), (9, '41', 'Đen', 15), (9, '42', 'Đen', 10),
(9, '37', 'Trắng', 12), (9, '38', 'Trắng', 18), (9, '39', 'Trắng', 22), (9, '40', 'Trắng', 18), (9, '41', 'Trắng', 12);

-- Sản phẩm 10: Vans Old Skool
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(10, '38', 'Đen/Trắng', 12), (10, '39', 'Đen/Trắng', 18), (10, '40', 'Đen/Trắng', 20), (10, '41', 'Đen/Trắng', 15), (10, '42', 'Đen/Trắng', 10);

-- Sản phẩm 11: Fila Disruptor 2
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(11, '38', 'Trắng', 10), (11, '39', 'Trắng', 15), (11, '40', 'Trắng', 18), (11, '41', 'Trắng', 12);

-- Sản phẩm 12: Nike Air Force 1 Shadow (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(12, '36', 'Trắng/Hồng', 8), (12, '37', 'Trắng/Hồng', 12), (12, '38', 'Trắng/Hồng', 15), (12, '39', 'Trắng/Hồng', 10);

-- Sản phẩm 13: Nike Court Legacy Lift (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(13, '36', 'Trắng', 10), (13, '37', 'Trắng', 15), (13, '38', 'Trắng', 18), (13, '39', 'Trắng', 12);

-- Sản phẩm 14: Adidas Stan Smith (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(14, '36', 'Trắng/Xanh', 10), (14, '37', 'Trắng/Xanh', 15), (14, '38', 'Trắng/Xanh', 18), (14, '39', 'Trắng/Xanh', 12);

-- Sản phẩm 15: Adidas NMD R1 (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(15, '36', 'Hồng', 6), (15, '37', 'Hồng', 10), (15, '38', 'Hồng', 12), (15, '39', 'Hồng', 8);

-- Sản phẩm 16: Puma Cali Star (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(16, '36', 'Trắng', 8), (16, '37', 'Trắng', 12), (16, '38', 'Trắng', 15), (16, '39', 'Trắng', 10);

-- Sản phẩm 17: New Balance 327 (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(17, '36', 'Kem', 8), (17, '37', 'Kem', 12), (17, '38', 'Kem', 15), (17, '39', 'Kem', 10);

-- Sản phẩm 18: Converse Chuck 70 Hi (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(18, '36', 'Đen', 10), (18, '37', 'Đen', 15), (18, '38', 'Đen', 18), (18, '39', 'Đen', 12);

-- Sản phẩm 19: Vans Sk8-Hi (Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(19, '36', 'Đen/Trắng', 8), (19, '37', 'Đen/Trắng', 12), (19, '38', 'Đen/Trắng', 15), (19, '39', 'Đen/Trắng', 10);

-- Sản phẩm 20: Nike Victori One Slide (Sandal Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(20, '36', 'Đen', 15), (20, '37', 'Đen', 20), (20, '38', 'Đen', 25), (20, '39', 'Đen', 18);

-- Sản phẩm 21: Fila Disruptor Shearling Boot (Boot Nữ)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(21, '36', 'Trắng', 5), (21, '37', 'Trắng', 8), (21, '38', 'Trắng', 10), (21, '39', 'Trắng', 6);

-- Sản phẩm 22: Vans Slip-On (Unisex)
INSERT INTO product_variants (product_id, size, color, stock_quantity) VALUES
(22, '37', 'Đen', 10), (22, '38', 'Đen', 15), (22, '39', 'Đen', 18), (22, '40', 'Đen', 15), (22, '41', 'Đen', 12), (22, '42', 'Đen', 8);


-- 7. Thêm mã giảm giá
INSERT INTO coupons (code, discount_type, discount_value, min_order_value, start_date, end_date, usage_limit, used_count, status) VALUES
('WELCOME10', 'percent', 10, 500000, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 1000, 0, 1),
('SALE20', 'percent', 20, 1000000, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 500, 0, 1),
('FREESHIP', 'fixed', 30000, 300000, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 2000, 0, 1),
('NEWYEAR50', 'fixed', 50000, 500000, '2024-12-01 00:00:00', '2025-01-31 23:59:59', 200, 0, 1),
('VIP30', 'percent', 30, 2000000, '2024-01-01 00:00:00', '2025-12-31 23:59:59', 100, 0, 1);

-- ==========================================================
-- PHẦN 3: HOÀN TẤT
-- ==========================================================

-- Bật lại safe update mode
SET SQL_SAFE_UPDATES = 1;

-- Thông báo hoàn tất
SELECT 'Database shop_giay_db đã được tạo thành công!' AS Message;
SELECT CONCAT('Tổng số sản phẩm: ', COUNT(*)) AS Products FROM products;
SELECT CONCAT('Tổng số biến thể: ', COUNT(*)) AS Variants FROM product_variants;
SELECT CONCAT('Tổng số danh mục: ', COUNT(*)) AS Categories FROM categories;
SELECT CONCAT('Tổng số thương hiệu: ', COUNT(*)) AS Brands FROM brands;
SELECT CONCAT('Tổng số mã giảm giá: ', COUNT(*)) AS Coupons FROM coupons;
