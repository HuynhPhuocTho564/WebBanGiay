-- Thêm cột cancel_reason vào bảng orders
ALTER TABLE orders ADD COLUMN cancel_reason TEXT DEFAULT NULL COMMENT 'Lý do hủy đơn hàng' AFTER status;
