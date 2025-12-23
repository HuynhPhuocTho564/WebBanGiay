-- Thêm cột status vào bảng brands
ALTER TABLE brands ADD COLUMN status TINYINT DEFAULT 1;

-- Cập nhật tất cả brands hiện tại thành hiển thị
UPDATE brands SET status = 1 WHERE status IS NULL;
