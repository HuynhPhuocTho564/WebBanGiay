-- ==========================================================
-- SỬA LỖI TRÙNG DANH MỤC VÀ THƯƠNG HIỆU
-- ==========================================================

-- Xóa danh mục trùng (giữ lại ID nhỏ nhất)
DELETE c1 FROM categories c1
INNER JOIN categories c2 
WHERE c1.id > c2.id AND c1.name = c2.name;

-- Xóa thương hiệu trùng (giữ lại ID nhỏ nhất)
DELETE b1 FROM brands b1
INNER JOIN brands b2 
WHERE b1.id > b2.id AND b1.name = b2.name;

-- Xem danh sách danh mục còn lại
SELECT * FROM categories ORDER BY id;

-- Xem danh sách thương hiệu còn lại
SELECT * FROM brands ORDER BY id;
