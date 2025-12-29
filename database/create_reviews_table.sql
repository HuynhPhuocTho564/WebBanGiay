-- =============================================
-- TẠO BẢNG REVIEWS (ĐÁNH GIÁ SẢN PHẨM)
-- =============================================

USE shop_giay_db;

-- Tạo bảng reviews nếu chưa có
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm một vài đánh giá mẫu
INSERT IGNORE INTO reviews (user_id, product_id, rating, comment) 
SELECT 
    (SELECT id FROM tblUser WHERE role = 0 LIMIT 1),
    id,
    FLOOR(4 + RAND()),
    'Sản phẩm rất tốt, đúng như mô tả!'
FROM products 
LIMIT 5;

SELECT 'Đã tạo bảng reviews thành công!' AS Result;
