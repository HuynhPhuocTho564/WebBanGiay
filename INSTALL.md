# HƯỚNG DẪN CÀI ĐẶT VÀ CHẠY WEBSITE BÁN GIÀY

## YÊU CẦU HỆ THỐNG

- **XAMPP** (hoặc WAMP/LAMP) với:
  - PHP >= 7.4
  - MySQL >= 5.7
  - Apache với mod_rewrite enabled
- **Trình duyệt web** (Chrome, Firefox, Edge...)

---

## BƯỚC 1: CÀI ĐẶT XAMPP

1. Tải XAMPP tại: https://www.apachefriends.org/download.html
2. Cài đặt XAMPP (mặc định vào `C:\xampp` hoặc `D:\xampp`)
3. Mở **XAMPP Control Panel**
4. Start **Apache** và **MySQL**

---

## BƯỚC 2: COPY PROJECT

Copy thư mục `shop_giay_project` vào:
```
D:\xampp\htdocs\shop_giay_project
```

Hoặc nếu XAMPP ở ổ C:
```
C:\xampp\htdocs\shop_giay_project
```

---

## BƯỚC 3: TẠO DATABASE

### Cách 1: Dùng phpMyAdmin (Dễ nhất)

1. Mở trình duyệt, truy cập: http://localhost/phpmyadmin
2. Click **"New"** (bên trái) để tạo database mới
3. Nhập tên: `shop_giay_db`
4. Chọn Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**
6. Chọn database `shop_giay_db` vừa tạo
7. Click tab **"Import"**
8. Click **"Choose File"** → chọn file `database/schema.sql`
9. Click **"Go"** để import

### Cách 2: Dùng MySQL Command Line

```bash
# Mở CMD, chạy:
cd D:\xampp\mysql\bin
mysql -u root -p

# Trong MySQL, chạy:
source D:/xampp/htdocs/shop_giay_project/database/schema.sql
```

---

## BƯỚC 4: CẤU HÌNH KẾT NỐI DATABASE

Mở file `config/config.php` và kiểm tra thông tin:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_giay_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Mặc định XAMPP không có password
define('DB_CHARSET', 'utf8mb4');
```

> **Lưu ý:** Nếu MySQL có password, thay đổi `DB_PASS` cho phù hợp.

---

## BƯỚC 5: CHẠY WEBSITE

Mở trình duyệt và truy cập:

```
http://localhost/shop_giay_project/public/
```

---

## TÀI KHOẢN ĐĂNG NHẬP

| Vai trò | Username | Password | Quyền hạn |
|---------|----------|----------|-----------|
| Admin | `admin` | `123456` | Toàn quyền |
| Nhân viên | `staff` | `123456` | Hạn chế (không xem báo cáo, không xóa) |
| Khách hàng | `user1` | `123456` | Chỉ mua hàng |

---

## CÁC ĐƯỜNG DẪN CHÍNH

| Trang | URL |
|-------|-----|
| Trang chủ | http://localhost/shop_giay_project/public/ |
| Sản phẩm | http://localhost/shop_giay_project/public/home/products |
| Đăng nhập | http://localhost/shop_giay_project/public/auth/login |
| Đăng ký | http://localhost/shop_giay_project/public/auth/register |
| Giỏ hàng | http://localhost/shop_giay_project/public/cart |
| Admin Panel | http://localhost/shop_giay_project/public/admin |

---

## XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi 1: "404 Not Found" hoặc "Page Not Found"

**Nguyên nhân:** mod_rewrite chưa được bật

**Cách sửa:**
1. Mở file `D:\xampp\apache\conf\httpd.conf`
2. Tìm dòng: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Xóa dấu `#` ở đầu dòng
4. Restart Apache trong XAMPP Control Panel

---

### Lỗi 2: "Access denied for user 'root'@'localhost'"

**Nguyên nhân:** Sai password MySQL

**Cách sửa:**
1. Mở file `config/config.php`
2. Sửa `DB_PASS` thành password đúng của MySQL

---

### Lỗi 3: "Database 'shop_giay_db' doesn't exist"

**Nguyên nhân:** Chưa tạo database

**Cách sửa:** Làm lại Bước 3

---

### Lỗi 4: Trang trắng không hiển thị gì

**Nguyên nhân:** Lỗi PHP

**Cách sửa:**
1. Mở file `D:\xampp\php\php.ini`
2. Tìm `display_errors = Off` → đổi thành `display_errors = On`
3. Restart Apache
4. Xem lỗi hiển thị và sửa

---

### Lỗi 5: Ảnh sản phẩm không hiển thị

**Nguyên nhân:** Đường dẫn ảnh trong database là URL bên ngoài (Unsplash)

**Cách sửa:** Đảm bảo có kết nối internet, hoặc thay ảnh local

---

## CẤU HÌNH ĐĂNG NHẬP GOOGLE (Tùy chọn)

1. Truy cập: https://console.cloud.google.com/
2. Tạo project mới
3. Vào **APIs & Services** → **Credentials**
4. Tạo **OAuth 2.0 Client ID**
5. Thêm Authorized redirect URI: 
   ```
   http://localhost/shop_giay_project/public/auth/googleCallback
   ```
6. Copy **Client ID** và **Client Secret**
7. Mở file `config/google_config.php` và cập nhật:

```php
define('GOOGLE_CLIENT_ID', 'your-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-client-secret');
define('GOOGLE_REDIRECT_URI', 'http://localhost/shop_giay_project/public/auth/googleCallback');
```

---

## HOÀN TẤT

Nếu làm đúng các bước trên, website sẽ chạy thành công tại:

**http://localhost/shop_giay_project/public/**

Chúc bạn thành công! 🎉
