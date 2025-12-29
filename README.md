# 👟 Sneaker Store - Website Bán Giày

Website thương mại điện tử bán giày sneaker với PHP Native + MySQL + Tailwind CSS.

## 📋 Yêu Cầu Hệ Thống

- **XAMPP** (PHP 8.0+, MySQL 8.0+, Apache)
- **Trình duyệt** hiện đại (Chrome, Firefox, Edge)

## 🚀 Hướng Dẫn Cài Đặt

### Bước 1: Clone/Copy Project

Copy thư mục project vào `C:\xampp\htdocs\shop_giay_project`

### Bước 2: Tạo Database

1. Mở **phpMyAdmin**: http://localhost/phpmyadmin
2. Tạo database mới: `shop_giay_db`
3. Chọn Collation: `utf8mb4_unicode_ci`

### Bước 3: Import Database

1. Chọn database `shop_giay_db`
2. Vào tab **Import**
3. Chọn file `database/schema.sql`
4. Click **Import**

### Bước 4: Cấu Hình Database

Mở file `config/config.php` và sửa thông tin:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_giay_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Mật khẩu MySQL của bạn
```

### Bước 5: Chạy Website

1. Khởi động **Apache** và **MySQL** trong XAMPP
2. Truy cập: http://localhost/shop_giay_project/public/

## 🔐 Tài Khoản Mặc Định

| Vai trò | Username | Mật khẩu |
|---------|----------|----------|
| Admin | `admin` | `123456` |
| Nhân viên | `staff` | `123456` |

## 📁 Cấu Trúc Thư Mục

```
shop_giay_project/
├── app/
│   ├── controllers/    # Xử lý logic
│   ├── models/         # Tương tác database
│   ├── core/           # Framework core
│   └── helpers/        # Hàm tiện ích
├── config/             # Cấu hình
├── database/           # File SQL
├── public/             # Entry point + assets
├── views/              # Giao diện
└── vendor/             # Thư viện
```

## ✨ Tính Năng

### Khách hàng
- Xem sản phẩm, tìm kiếm, lọc
- Giỏ hàng, đặt hàng
- Mã giảm giá
- Quản lý đơn hàng
- Yêu thích sản phẩm
- Đăng nhập Google

### Admin
- Dashboard thống kê
- Quản lý sản phẩm, danh mục, thương hiệu
- Quản lý đơn hàng
- Quản lý người dùng
- Quản lý mã giảm giá
- Báo cáo doanh thu

### Nhân viên
- Dashboard (không xem doanh thu)
- Quản lý sản phẩm (không xóa)
- Quản lý đơn hàng
- Quản lý danh mục, thương hiệu

## 🔗 Đường Dẫn

| Trang | URL |
|-------|-----|
| Trang chủ | `/public/` |
| Sản phẩm | `/public/products` |
| Đăng nhập | `/public/auth/login` |
| Admin | `/public/admin` |

## ⚙️ Cấu Hình Google Login (Tùy chọn)

1. Tạo project tại [Google Cloud Console](https://console.cloud.google.com)
2. Tạo OAuth 2.0 credentials
3. Sửa file `config/google_config.php`:

```php
define('GOOGLE_CLIENT_ID', 'your-client-id');
define('GOOGLE_CLIENT_SECRET', 'your-client-secret');
define('GOOGLE_REDIRECT_URI', 'http://localhost/shop_giay_project/public/auth/googleCallback');
```

## 📝 Ghi Chú

- Đảm bảo `mod_rewrite` đã bật trong Apache
- Thư mục `public/uploads` cần quyền ghi (777 hoặc 755)
- Nếu lỗi 404, kiểm tra file `.htaccess`

## 👨‍💻 Công Nghệ

- **Backend:** PHP 8.0 Native (MVC Pattern)
- **Database:** MySQL 8.0
- **Frontend:** Tailwind CSS, JavaScript
- **Icons:** Heroicons, Font Awesome
