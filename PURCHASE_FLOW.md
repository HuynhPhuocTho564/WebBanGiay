# MÔ TẢ CHI TIẾT CHỨC NĂNG MUA HÀNG

---

## TỔNG QUAN QUY TRÌNH MUA HÀNG

```
Xem sản phẩm → Chọn màu/size → Thêm giỏ hàng → Xem giỏ hàng → Thanh toán → Đặt hàng thành công
```

---

## BƯỚC 1: XEM CHI TIẾT SẢN PHẨM

**URL:** `/home/product/{slug}`

**Mô tả:**
- Khách hàng click vào sản phẩm từ trang chủ hoặc danh sách sản phẩm
- Hệ thống hiển thị trang chi tiết sản phẩm

**Thông tin hiển thị:**
- Tên sản phẩm
- Giá gốc và giá khuyến mãi (nếu có)
- Phần trăm giảm giá
- Ảnh sản phẩm (thumbnail + gallery)
- Mô tả sản phẩm
- Thương hiệu
- Danh mục
- Lượt xem
- Đánh giá trung bình

---

## BƯỚC 2: CHỌN MÀU SẮC VÀ SIZE

**Mô tả:**
- Sản phẩm có nhiều biến thể (variant) theo màu và size
- Mỗi variant có số lượng tồn kho riêng

**Quy trình:**

1. Hệ thống load tất cả variants của sản phẩm từ bảng `product_variants`
2. Hiển thị danh sách màu sắc dưới dạng radio button
3. Khách chọn màu → Hệ thống filter các size có sẵn của màu đó
4. Hiển thị danh sách size:
   - Size còn hàng: Có thể click chọn
   - Size hết hàng (stock = 0): Disable, hiện "Hết hàng"
5. Khách chọn size → Hệ thống tìm variant tương ứng
6. Hiển thị số lượng tồn kho: "Còn X sản phẩm"

**Ví dụ:**
```
Sản phẩm: Nike Air Max 90
Variants trong database:
- ID 1: Đen, Size 40, Tồn kho: 5
- ID 2: Đen, Size 41, Tồn kho: 3
- ID 3: Đen, Size 42, Tồn kho: 0 (hết hàng)
- ID 4: Trắng, Size 40, Tồn kho: 8

Khách chọn màu "Đen":
→ Hiển thị size: [40 - còn hàng] [41 - còn hàng] [42 - hết hàng]

Khách chọn size "41":
→ Tìm variant: color="Đen" AND size="41" → ID 2
→ Hiển thị: "Còn 3 sản phẩm"
```

---

## BƯỚC 3: THÊM VÀO GIỎ HÀNG

**Mô tả:**
- Khách nhập số lượng muốn mua
- Click nút "Thêm vào giỏ"
- Hệ thống thêm sản phẩm vào giỏ hàng (lưu trong SESSION)

**Quy trình xử lý:**

1. Khách click "Thêm vào giỏ"
2. JavaScript gửi AJAX POST đến `/cart/add`
3. Dữ liệu gửi đi:
   - variant_id: ID của variant đã chọn
   - quantity: Số lượng muốn mua
4. Server xử lý:
   - Kiểm tra variant có tồn tại không
   - Kiểm tra số lượng tồn kho có đủ không
   - Nếu sản phẩm đã có trong giỏ → Cộng thêm số lượng
   - Nếu chưa có → Thêm mới vào giỏ
5. Trả về response JSON:
   - success: true/false
   - message: Thông báo
   - cartCount: Số lượng sản phẩm trong giỏ
6. Frontend cập nhật:
   - Hiển thị toast thông báo "Đã thêm vào giỏ hàng"
   - Cập nhật số lượng trên icon giỏ hàng

**Cấu trúc giỏ hàng trong SESSION:**
```php
$_SESSION['cart'] = [
    [
        'variant_id' => 2,
        'product_id' => 1,
        'product_name' => 'Nike Air Max 90',
        'color' => 'Đen',
        'size' => '41',
        'price' => 2990000,
        'quantity' => 2,
        'thumbnail' => 'nike-air-max.jpg'
    ],
    // ... các sản phẩm khác
];
```

---

## BƯỚC 4: XEM GIỎ HÀNG

**URL:** `/cart`

**Mô tả:**
- Khách xem lại các sản phẩm đã thêm vào giỏ
- Có thể thay đổi số lượng hoặc xóa sản phẩm

**Thông tin hiển thị mỗi sản phẩm:**
- Ảnh thumbnail
- Tên sản phẩm
- Màu sắc
- Size
- Đơn giá
- Số lượng (có nút +/-)
- Thành tiền (đơn giá x số lượng)
- Nút xóa

**Tổng kết:**
- Tạm tính: Tổng tiền tất cả sản phẩm
- Nút "Tiếp tục mua sắm": Quay lại trang sản phẩm
- Nút "Thanh toán": Chuyển sang trang checkout

**Các thao tác:**

1. Thay đổi số lượng:
   - Click nút +/- hoặc nhập trực tiếp
   - AJAX gửi request cập nhật
   - Tự động tính lại thành tiền và tổng

2. Xóa sản phẩm:
   - Click nút xóa
   - Xác nhận xóa
   - AJAX gửi request xóa
   - Sản phẩm biến mất, tổng tiền cập nhật

---

## BƯỚC 5: THANH TOÁN

**URL:** `/checkout`

**Điều kiện:** Phải đăng nhập

**Mô tả:**
- Khách điền thông tin giao hàng
- Chọn phương thức thanh toán
- Áp dụng mã giảm giá (nếu có)
- Xác nhận đặt hàng

**Form thông tin giao hàng:**
- Họ tên người nhận (bắt buộc)
- Số điện thoại (bắt buộc)
- Tỉnh/Thành phố
- Quận/Huyện
- Địa chỉ chi tiết (bắt buộc)
- Ghi chú đơn hàng (tùy chọn)

**Phương thức thanh toán:**
- COD (Thanh toán khi nhận hàng)
- Chuyển khoản ngân hàng

**Mã giảm giá:**
- Nhập mã vào ô input
- Click "Áp dụng"
- Hệ thống kiểm tra:
  - Mã có tồn tại không
  - Mã còn hiệu lực không (ngày bắt đầu - kết thúc)
  - Mã còn lượt sử dụng không
  - Đơn hàng có đạt giá trị tối thiểu không
- Nếu hợp lệ: Hiển thị số tiền được giảm
- Nếu không hợp lệ: Hiển thị thông báo lỗi

**Chi tiết đơn hàng:**
- Danh sách sản phẩm (tên, màu, size, số lượng, giá)
- Tạm tính
- Giảm giá (nếu có mã)
- Phí vận chuyển
- Tổng cộng

---

## BƯỚC 6: XÁC NHẬN ĐẶT HÀNG

**Mô tả:**
- Khách click nút "Đặt hàng"
- Hệ thống xử lý đơn hàng

**Quy trình xử lý phía Server:**

```
1. BẮT ĐẦU TRANSACTION
   └── Đảm bảo tất cả thao tác thành công hoặc rollback toàn bộ

2. VALIDATE DỮ LIỆU
   ├── Kiểm tra thông tin giao hàng đầy đủ
   ├── Kiểm tra giỏ hàng không rỗng
   └── Kiểm tra CSRF token

3. KIỂM TRA VÀ TRỪ TỒN KHO (cho từng sản phẩm)
   ├── SELECT stock_quantity FROM product_variants WHERE id = ? FOR UPDATE
   │   └── FOR UPDATE: Lock row, người khác phải đợi
   ├── Kiểm tra stock_quantity >= quantity đặt mua
   │   └── Nếu không đủ → throw Exception → Rollback
   └── UPDATE product_variants SET stock_quantity = stock_quantity - quantity

4. KIỂM TRA MÃ GIẢM GIÁ (nếu có)
   ├── Kiểm tra mã còn hiệu lực
   ├── Kiểm tra còn lượt sử dụng
   ├── Tính số tiền được giảm
   └── Cập nhật số lần đã dùng của mã

5. TÍNH TỔNG TIỀN
   ├── Tạm tính = Tổng (đơn giá x số lượng)
   ├── Giảm giá = Số tiền từ coupon
   ├── Phí ship = Tính theo khu vực (hoặc miễn phí)
   └── Tổng cộng = Tạm tính - Giảm giá + Phí ship

6. TẠO ĐƠN HÀNG
   └── INSERT INTO orders (
         user_id, order_code, fullname, phone, address,
         note, subtotal, discount, shipping_fee, total,
         payment_method, status, created_at
       )

7. TẠO CHI TIẾT ĐƠN HÀNG (cho từng sản phẩm)
   └── INSERT INTO order_items (
         order_id, variant_id, product_name, color, size,
         price, quantity
       )

8. COMMIT TRANSACTION
   └── Lưu vĩnh viễn tất cả thay đổi

9. XÓA GIỎ HÀNG
   └── unset($_SESSION['cart'])

10. REDIRECT TRANG THÀNH CÔNG
    └── /checkout/success/{order_id}
```

**Xử lý lỗi:**
- Nếu bất kỳ bước nào lỗi → ROLLBACK
- Tồn kho được hoàn lại
- Đơn hàng không được tạo
- Hiển thị thông báo lỗi cho khách

---

## BƯỚC 7: ĐẶT HÀNG THÀNH CÔNG

**URL:** `/checkout/success/{order_id}`

**Mô tả:**
- Hiển thị thông báo đặt hàng thành công
- Hiển thị mã đơn hàng
- Hiển thị thông tin đơn hàng tóm tắt

**Thông tin hiển thị:**
- Mã đơn hàng: ORD1234567890
- Ngày đặt: 24/12/2024
- Tổng tiền: 2,990,000đ
- Phương thức thanh toán: COD
- Trạng thái: Chờ xác nhận

**Các nút:**
- "Xem chi tiết đơn hàng": Chuyển đến trang quản lý đơn hàng
- "Tiếp tục mua sắm": Quay lại trang chủ

---

## XỬ LÝ RACE CONDITION

**Vấn đề:**
- 2 người đặt cùng sản phẩm cùng lúc
- Sản phẩm chỉ còn 1 đôi
- Cả 2 đều thấy "còn hàng" và đặt thành công → Lỗi!

**Giải pháp: SELECT ... FOR UPDATE**

```sql
-- Khi đọc tồn kho, lock row lại
SELECT stock_quantity FROM product_variants 
WHERE id = 2 FOR UPDATE;

-- Row bị lock, người khác phải đợi
-- Sau khi commit/rollback mới unlock
```

**Ví dụ:**
```
Thời điểm T0: Sản phẩm còn 1 đôi

User A (T1): SELECT ... FOR UPDATE → stock = 1, lock row
User B (T2): SELECT ... FOR UPDATE → Đợi (row đang bị lock)

User A (T3): UPDATE stock = 0, COMMIT → Unlock row
User B (T4): SELECT ... FOR UPDATE → stock = 0 → Báo lỗi "Hết hàng"
```

---

## CẤU TRÚC DATABASE LIÊN QUAN

**Bảng orders:**
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_code VARCHAR(50) UNIQUE,
    fullname VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    note TEXT,
    subtotal DECIMAL(12,2),
    discount DECIMAL(12,2) DEFAULT 0,
    shipping_fee DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2),
    coupon_id INT,
    payment_method ENUM('cod', 'bank_transfer'),
    payment_status ENUM('pending', 'paid') DEFAULT 'pending',
    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'return_requested', 'returned') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Bảng order_items:**
```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    variant_id INT NOT NULL,
    product_name VARCHAR(255),
    color VARCHAR(50),
    size VARCHAR(10),
    price DECIMAL(12,2),
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

---

## TRẠNG THÁI ĐƠN HÀNG

| Trạng thái | Mô tả | Hành động tiếp theo |
|------------|-------|---------------------|
| pending | Chờ xác nhận | Admin duyệt hoặc Khách hủy |
| confirmed | Đã xác nhận | Admin chuyển giao hàng |
| shipping | Đang giao | Admin xác nhận hoàn thành |
| completed | Hoàn thành | Khách có thể yêu cầu trả hàng |
| cancelled | Đã hủy | Kết thúc, hoàn kho |
| return_requested | Yêu cầu trả hàng | Admin xử lý |
| returned | Đã trả hàng | Kết thúc, hoàn kho |
