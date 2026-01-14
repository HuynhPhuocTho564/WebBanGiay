-- ==========================================================
-- BẢNG AUDIT LOGS - GHI LẠI LỊCH SỬ HOẠT ĐỘNG
-- ==========================================================

USE shop_giay_db;

-- Tạo bảng audit_logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    username VARCHAR(100) DEFAULT NULL,
    action VARCHAR(50) NOT NULL COMMENT 'login, logout, create, update, delete, etc.',
    entity_type VARCHAR(50) DEFAULT NULL COMMENT 'product, order, user, category, etc.',
    entity_id INT DEFAULT NULL,
    description TEXT NOT NULL,
    old_data JSON DEFAULT NULL COMMENT 'Dữ liệu trước khi thay đổi',
    new_data JSON DEFAULT NULL COMMENT 'Dữ liệu sau khi thay đổi',
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);

-- Thông báo hoàn tất
SELECT 'Bảng audit_logs đã được tạo thành công!' AS Message;
