# MÔ TẢ ĐỀ TÀI

## TÊN ĐỀ TÀI
**Website Bán Giày Sneaker Store**

---

## 1. LÝ DO CHỌN ĐỀ TÀI

### 1.1. Thực trạng thị trường
- Thương mại điện tử đang phát triển mạnh mẽ tại Việt Nam
- Nhu cầu mua sắm online ngày càng tăng, đặc biệt sau đại dịch COVID-19
- Giày sneaker là mặt hàng thời trang được giới trẻ ưa chuộng
- Các thương hiệu lớn như Nike, Adidas, Converse có lượng fan đông đảo

### 1.2. Vấn đề cần giải quyết
- Cửa hàng truyền thống bị giới hạn về không gian, thời gian
- Khách hàng muốn xem và so sánh nhiều sản phẩm cùng lúc
- Cần hệ thống quản lý tồn kho theo size và màu sắc
- Cần theo dõi đơn hàng và quản lý khách hàng hiệu quả

### 1.3. Mục tiêu đề tài
- Xây dựng website bán giày hoàn chỉnh cho cả khách hàng và quản trị viên
- Áp dụng kiến thức lập trình web đã học vào thực tế
- Hiểu sâu về mô hình MVC và cách xây dựng từ đầu
- Giải quyết bài toán quản lý tồn kho theo biến thể sản phẩm

---

## 2. PHẠM VI ĐỀ TÀI

### 2.1. Đối tượng sử dụng
- **Khách hàng:** Người mua giày online
- **Nhân viên:** Quản lý đơn hàng, sản phẩm
- **Quản trị viên:** Toàn quyền quản lý hệ thống

### 2.2. Phạm vi chức năng

**Phía khách hàng:**
- Đăng ký, đăng nhập (bao gồm Google)
- Xem và tìm kiếm sản phẩm
- Lọc sản phẩm theo danh mục, thương hiệu, giá, giới tính
- Xem chi tiết sản phẩm, chọn màu và size
- Thêm vào giỏ hàng, thanh toán
- Quản lý đơn hàng cá nhân
- Đánh giá sản phẩm

**Phía quản trị:**
- Dashboard thống kê
- Quản lý sản phẩm (CRUD, variants, gallery)
- Quản lý danh mục, thương hiệu
- Quản lý đơn hàng
- Quản lý người dùng, phân quyền
- Quản lý mã giảm giá
- Báo cáo doanh thu

### 2.3. Giới hạn đề tài
- Không tích hợp thanh toán online (VNPay, Momo)
- Không có chức năng chat trực tuyến
- Không có app mobile
- Chỉ hỗ trợ tiếng Việt

---

## 3. CÔNG NGHỆ SỬ DỤNG

### 3.1. Backend
- **Ngôn ngữ:** PHP 7.4+
- **Kiến trúc:** MVC tự xây dựng (không dùng framework)
- **Database:** MySQL 5.7+
- **Kết nối DB:** PDO (PHP Data Objects)

### 3.2. Frontend
- **HTML5, CSS3, JavaScript**
- **Tailwind CSS:** Framework CSS utility-first
- **Chart.js:** Vẽ biểu đồ thống kê

### 3.3. Tích hợp
- **Google OAuth 2.0:** Đăng nhập bằng Google

### 3.4. Công cụ phát triển
- **XAMPP:** Apache + MySQL + PHP
- **Visual Studio Code:** Code editor
- **MySQL Workbench:** Quản lý database
- **Git:** Quản lý phiên bản

---

## 4. KIẾN TRÚC HỆ THỐNG

### 4.1. Mô hình MVC

```
┌─────────────────────────────────────────────────────────┐
│                      BROWSER                             │
│                    (Client Request)                      │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                      ROUTER                              │
│                    (App.php)                             │
│         Parse URL → Xác định Controller/Method           │
└─────────────────────────┬───────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────┐
│                    CONTROLLER                            │
│              (HomeController.php, ...)                   │
│         Nhận request → Xử lý logic → Trả response        │
└──────────┬──────────────────────────────────┬───────────┘
           │                                  │
           ▼                                  ▼
┌──────────────────────┐          ┌──────────────────────┐
│        MODEL         │          │         VIEW         │
│  (ProductModel.php)  │          │   (product.php)      │
│  Tương tác Database  │          │   Render HTML        │
└──────────┬───────────┘          └──────────────────────┘
           │
           ▼
┌──────────────────────┐
│       DATABASE       │
│       (MySQL)        │
└──────────────────────┘
```

### 4.2. Cấu trúc thư mục

```
shop_giay_project/
├── app/
│   ├── core/           # Lõi hệ thống
│   ├── controllers/    # Xử lý logic
│   ├── models/         # Tương tác DB
│   └── helpers/        # Hàm tiện ích
├── views/              # Giao diện
├── config/             # Cấu hình
├── public/             # Assets, uploads
└── database/           # SQL scripts
```

---

## 5. THIẾT KẾ CƠ SỞ DỮ LIỆU

### 5.1. Sơ đồ ERD (Entity Relationship Diagram)

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│  categories │       │   brands    │       │   tblUser   │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │       │ id (PK)     │       │ id (PK)     │
│ name        │       │ name        │       │ email       │
│ slug        │       │ slug        │       │ password    │
│ image       │       │ logo        │       │ fullname    │
└──────┬──────┘       └──────┬──────┘       │ role        │
       │                     │              │ status      │
       │    ┌────────────────┘              └──────┬──────┘
       │    │                                      │
       ▼    ▼                                      │
┌─────────────────┐                                │
│    products     │                                │
├─────────────────┤                                │
│ id (PK)         │                                │
│ name            │                                │
│ slug            │                                │
│ price           │                                │
│ discount_price  │                                │
│ category_id(FK) │                                │
│ brand_id (FK)   │                                │
└────────┬────────┘                                │
         │                                         │
         ▼                                         │
┌─────────────────────┐                            │
│  product_variants   │                            │
├─────────────────────┤                            │
│ id (PK)             │                            │
│ product_id (FK)     │                            │
│ color               │                            │
│ size                │                            │
│ stock_quantity      │◄───────────────────────────┤
│ sku                 │                            │
└─────────┬───────────┘                            │
          │                                        │
          │         ┌──────────────────────────────┘
          │         │
          ▼         ▼
┌─────────────────────┐       ┌─────────────────┐
│      orders         │       │     reviews     │
├─────────────────────┤       ├─────────────────┤
│ id (PK)             │       │ id (PK)         │
│ user_id (FK)        │       │ user_id (FK)    │
│ order_code          │       │ product_id (FK) │
│ total               │       │ rating          │
│ status              │       │ comment         │
└─────────┬───────────┘       └─────────────────┘
          │
          ▼
┌─────────────────────┐
│    order_items      │
├─────────────────────┤
│ id (PK)             │
│ order_id (FK)       │
│ variant_id (FK)     │
│ quantity            │
│ price               │
└─────────────────────┘
```

### 5.2. Danh sách bảng

| STT | Tên bảng | Mô tả |
|-----|----------|-------|
| 1 | tblUser | Người dùng (khách hàng, nhân viên, admin) |
| 2 | categories | Danh mục sản phẩm |
| 3 | brands | Thương hiệu |
| 4 | products | Sản phẩm |
| 5 | product_variants | Biến thể sản phẩm (màu, size, tồn kho) |
| 6 | product_gallery | Gallery ảnh sản phẩm |
| 7 | orders | Đơn hàng |
| 8 | order_items | Chi tiết đơn hàng |
| 9 | coupons | Mã giảm giá |
| 10 | reviews | Đánh giá sản phẩm |
| 11 | wishlist | Danh sách yêu thích |

### 5.3. Điểm đặc biệt: Bảng product_variants

**Vấn đề:** Một sản phẩm giày có nhiều biến thể (màu + size), mỗi biến thể có tồn kho riêng.

**Giải pháp:** Tách riêng bảng `product_variants` để quản lý từng biến thể.

**Ví dụ:**
- Nike Air Max 90 / Đen / Size 40: còn 5 đôi
- Nike Air Max 90 / Đen / Size 41: còn 3 đôi
- Nike Air Max 90 / Trắng / Size 40: còn 8 đôi

**Lợi ích:**
- Quản lý tồn kho chính xác đến từng size-màu
- Khi đặt hàng, trừ đúng variant khách chọn
- Dễ thống kê size/màu nào bán chạy

---

## 6. CÁC CHỨC NĂNG CHÍNH

### 6.1. Chức năng khách hàng (42 chức năng)
- Đăng ký, đăng nhập, đăng nhập Google
- Xem, tìm kiếm, lọc sản phẩm
- Xem chi tiết, chọn màu/size
- Giỏ hàng, thanh toán
- Quản lý đơn hàng, đánh giá

### 6.2. Chức năng quản trị (40 chức năng)
- Dashboard thống kê
- CRUD sản phẩm, danh mục, thương hiệu
- Quản lý đơn hàng, người dùng
- Mã giảm giá, báo cáo

---

## 7. BẢO MẬT

- **CSRF Token:** Chống tấn công giả mạo request
- **Password Hashing:** Mã hóa mật khẩu bằng bcrypt
- **Prepared Statements:** Chống SQL Injection
- **XSS Prevention:** Escape output với htmlspecialchars
- **Session Security:** Regenerate ID, timeout 30 phút
- **Phân quyền:** 3 cấp (Customer, Staff, Admin)

---

## 8. KẾT QUẢ ĐẠT ĐƯỢC

### 8.1. Sản phẩm
- Website hoàn chỉnh với 82 chức năng
- Giao diện responsive, thân thiện
- Hệ thống quản trị đầy đủ

### 8.2. Kiến thức
- Hiểu sâu mô hình MVC
- Thiết kế database chuẩn hóa
- Xử lý transaction, race condition
- Tích hợp OAuth 2.0
- Áp dụng các biện pháp bảo mật

### 8.3. Kỹ năng
- Lập trình PHP thuần
- Thiết kế giao diện với Tailwind CSS
- Làm việc với MySQL
- Sử dụng Git quản lý code

---

## 9. HƯỚNG PHÁT TRIỂN

- Tích hợp thanh toán online (VNPay, Momo, ZaloPay)
- Thêm chức năng chat với khách hàng
- Phát triển app mobile (React Native)
- Tích hợp AI gợi ý sản phẩm
- Hỗ trợ đa ngôn ngữ
- Tối ưu SEO
