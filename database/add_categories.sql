-- ==========================================================
-- THÊM 2 DANH MỤC CÒN THIẾU ĐỂ ĐỦ 7 DANH MỤC
-- ==========================================================

-- Thêm Sandal và Boot (nếu chưa có)
INSERT IGNORE INTO categories (name, slug, status) VALUES
('Sandal', 'sandal', 1),
('Boot', 'boot', 1);

-- Kiểm tra kết quả - phải có 7 danh mục
SELECT id, name, slug FROM categories ORDER BY name;
