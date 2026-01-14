-- Thêm cột coupon_id vào bảng orders
-- Chạy file này trong MySQL Workbench hoặc command line

USE shop_giay_db;

-- Thêm cột coupon_id nếu chưa có
ALTER TABLE orders ADD COLUMN coupon_id INT DEFAULT NULL AFTER payment_method;

-- Thêm foreign key (tùy chọn)
ALTER TABLE orders ADD CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL;

SELECT 'Đã thêm cột coupon_id vào bảng orders!' AS Message;
