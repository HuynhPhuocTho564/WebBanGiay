-- Sửa lỗi avatar hiển thị text "Avatar" thay vì ảnh
-- Chạy file này để fix dữ liệu trong database

-- Đặt avatar = NULL cho các user có avatar là text placeholder
UPDATE tblUser SET avatar = NULL WHERE avatar = 'Avatar' OR avatar = 'avatar' OR avatar = '';

-- Kiểm tra kết quả
SELECT id, username, fullname, avatar FROM tblUser;
