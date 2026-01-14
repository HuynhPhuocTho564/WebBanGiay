-- =====================================================
-- CẬP NHẬT BẢNG COUPONS CHO MYSQL SERVER
-- =====================================================

USE shop_giay_db;

-- Xóa bảng cũ và tạo lại với đầy đủ các cột
DROP TABLE IF EXISTS coupons;

CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    discount_type ENUM('percent', 'fixed') DEFAULT 'percent',
    discount_value DECIMAL(12, 2) NOT NULL,
    max_discount DECIMAL(12, 2) DEFAULT NULL,
    min_order_value DECIMAL(12, 2) DEFAULT 0,
    start_date DATETIME,
    end_date DATETIME,
    usage_limit INT DEFAULT 100,
    used_count INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Thêm dữ liệu mẫu
INSERT INTO coupons (code, name, description, discount_type, discount_value, max_discount, min_order_value, start_date, end_date, usage_limit, used_count, status) VALUES
('WELCOME10', 'Giảm 10% cho khách mới', 'Áp dụng cho đơn hàng đầu tiên', 'percent', 10, 100000, 500000, '2024-01-01 00:00:00', '2026-12-31 23:59:59', 1000, 0, 1),
('SALE20', 'Giảm 20% đơn từ 1 triệu', 'Ưu đãi đặc biệt cuối tuần', 'percent', 20, 200000, 1000000, '2024-01-01 00:00:00', '2026-12-31 23:59:59', 500, 0, 1),
('GIAM30K', 'Giảm ngay 30K', 'Áp dụng cho đơn từ 300K', 'fixed', 30000, NULL, 300000, '2024-01-01 00:00:00', '2026-12-31 23:59:59', 2000, 0, 1),
('GIAM50K', 'Giảm ngay 50K', 'Áp dụng cho đơn từ 500K', 'fixed', 50000, NULL, 500000, '2024-01-01 00:00:00', '2026-12-31 23:59:59', 500, 0, 1),
('VIP30', 'VIP giảm 30%', 'Dành cho khách VIP, đơn từ 2 triệu', 'percent', 30, 500000, 2000000, '2024-01-01 00:00:00', '2026-12-31 23:59:59', 100, 0, 1);

SELECT 'Bảng coupons đã được tạo thành công!' AS Message;
SELECT * FROM coupons;
