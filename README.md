# 👟 Sneaker Store - Website Bán Giày

Website thương mại điện tử bán giày sneaker với PHP Native + MySQL + Tailwind CSS.

---

## 🚀 HƯỚNG DẪN CHẠY WEBSITE

### Bước 1: Cài đặt XAMPP

1. Tải XAMPP tại: https://www.apachefriends.org/download.html
2. Cài đặt XAMPP (khuyến nghị: `D:\xampp`)
3. Mở **XAMPP Control Panel**

### Bước 2: Copy Project

Copy thư mục `shop_giay_project` vào:
```
D:\xampp\htdocs\shop_giay_project
```

### Bước 3: Khởi động Server

Trong **XAMPP Control Panel**:
1. Click **Start** ở dòng **Apache** → chờ chuyển xanh
2. Click **Start** ở dòng **MySQL** → chờ chuyển xanh

![XAMPP](https://i.imgur.com/example.png)

### Bước 4: Tạo Database

1. Mở trình duyệt → truy cập: **http://localhost/phpmyadmin**
2. Click **New** (bên trái)
3. Database name: `shop_giay_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click **Create**

### Bước 5: Import Database

1. Click chọn database `shop_giay_db` (bên trái)
2. Click tab **Import** (trên cùng)
3. Click **Choose File** → chọn file `database/schema.sql`
4. Kéo xuống dưới → Click **Import**
5. Chờ thông báo "Import has been successfully finished"

### Bước 6: Mở Website

Mở trình duyệt và truy cập:

```
http://localhost/shop_giay_project/public/
```

✅ **Done!** Website đã chạy.

---

## 🔐 TÀI KHOẢN ĐĂNG NHẬP

| Vai trò | Username | Password | Quyền |
|---------|----------|----------|-------|
| Admin | `admin` | `123456` | Toàn quyền |
| Nhân viên | `staff` | `123456` | Hạn chế |
| Khách hàng | `user1` | `123456` | Mua hàng |

---

## 🔗 CÁC TRANG CHÍNH

| Trang | URL |
|-------|-----|
| Trang chủ | http://localhost/shop_giay_project/public/ |
| Sản phẩm | http://localhost/shop_giay_project/public/home/products |
| Đăng nhập | http://localhost/shop_giay_project/public/auth/login |
| Admin Panel | http://localhost/shop_giay_project/public/admin |

---

## ❌ XỬ LÝ LỖI

### Lỗi "404 Not Found"
→ Bật mod_rewrite: Mở `D:\xampp\apache\conf\httpd.conf`, tìm dòng `#LoadModule rewrite_module`, xóa dấu `#`, restart Apache.

### Lỗi "Access denied for user 'root'"
→ Sửa password trong `config/config.php`

### Lỗi "Database doesn't exist"
→ Làm lại Bước 4, 5

---

## � CẤờU TRÚC

```
shop_giay_project/
├── app/
│   ├── controllers/    # Xử lý logic
│   ├── models/         # Database
│   ├── core/           # Framework
│   └── helpers/        # Hàm tiện ích
├── config/             # Cấu hình
├── database/           # File SQL
├── public/             # Entry point
└── views/              # Giao diện
```

---

## ✨ TÍNH NĂNG

**Khách hàng:** Xem sản phẩm, tìm kiếm, lọc, giỏ hàng, đặt hàng, mã giảm giá, yêu thích, đăng nhập Google

**Admin:** Dashboard, quản lý sản phẩm/danh mục/thương hiệu/đơn hàng/user/mã giảm giá, báo cáo doanh thu

**Nhân viên:** Như Admin nhưng không xem doanh thu, không xóa

---

## 👨‍💻 CÔNG NGHỆ

- PHP 8.0 Native (MVC)
- MySQL 8.0
- Tailwind CSS
- JavaScript
