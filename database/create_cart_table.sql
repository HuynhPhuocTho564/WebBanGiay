-- ==========================================================
-- BẢNG CART - LƯU GIỎ HÀNG VÀO DATABASE
-- ==========================================================

USE shop_giay_db;

-- Tạo bảng cart
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_variant_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_variant (user_id, product_variant_id),
    FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Thông báo hoàn tất
SELECT 'Bảng cart đã được tạo thành công!' AS Message;
