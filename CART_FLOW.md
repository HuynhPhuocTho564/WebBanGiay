# MÔ TẢ CHI TIẾT CHỨC NĂNG GIỎ HÀNG

---

## TỔNG QUAN

Giỏ hàng là nơi lưu trữ tạm thời các sản phẩm khách hàng muốn mua trước khi tiến hành thanh toán.

**Đặc điểm:**
- Lưu trong SESSION (không cần đăng nhập vẫn thêm được)
- Mất khi đóng trình duyệt hoặc session hết hạn
- Mỗi sản phẩm lưu theo variant (màu + size cụ thể)

---

## CẤU TRÚC DỮ LIỆU GIỎ HÀNG

**Lưu trong SESSION:**
```php
$_SESSION['cart'] = [
    [
        'variant_id' => 2,              // ID của variant trong bảng product_variants
        'product_id' => 1,              // ID sản phẩm
        'product_name' => 'Nike Air Max 90',  // Tên sản phẩm
        'color' => 'Đen',               // Màu sắc
        'size' => '41',                 // Size
        'price' => 2990000,             // Giá tại thời điểm thêm
        'quantity' => 2,                // Số lượng
        'thumbnail' => 'nike-air-max.jpg'  // Ảnh thumbnail
    ],
    [
        'variant_id' => 5,
        'product_id' => 1,
        'product_name' => 'Nike Air Max 90',
        'color' => 'Trắng',
        'size' => '41',
        'price' => 2990000,
        'quantity' => 1,
        'thumbnail' => 'nike-air-max.jpg'
    ],
    [
        'variant_id' => 10,
        'product_id' => 3,
        'product_name' => 'Adidas Ultraboost',
        'color' => 'Đen',
        'size' => '42',
        'price' => 3500000,
        'quantity' => 1,
        'thumbnail' => 'adidas-ultraboost.jpg'
    ]
];
```

**Giải thích:**
- Cùng 1 sản phẩm nhưng khác màu/size → 2 item riêng biệt
- Mỗi item có variant_id riêng để trừ đúng tồn kho khi đặt hàng

---

## CHỨC NĂNG 1: THÊM SẢN PHẨM VÀO GIỎ

**URL:** POST `/cart/add`

**Dữ liệu gửi lên:**
```
variant_id: 2
quantity: 1
```

**Quy trình xử lý:**

```
1. NHẬN DỮ LIỆU
   ├── variant_id: ID của variant (màu + size)
   └── quantity: Số lượng muốn thêm

2. KIỂM TRA VARIANT TỒN TẠI
   └── SELECT pv.*, p.name, p.price, p.discount_price, p.thumbnail
       FROM product_variants pv
       JOIN products p ON pv.product_id = p.id
       WHERE pv.id = ?
   
   Nếu không tồn tại → Trả về lỗi

3. KIỂM TRA TỒN KHO
   └── Nếu stock_quantity < quantity → Trả về lỗi "Không đủ hàng"

4. KIỂM TRA ĐÃ CÓ TRONG GIỎ CHƯA
   ├── Duyệt qua $_SESSION['cart']
   └── Tìm item có variant_id trùng

5. NẾU ĐÃ CÓ → CỘNG THÊM SỐ LƯỢNG
   └── $item['quantity'] += $quantity

6. NẾU CHƯA CÓ → THÊM MỚI
   └── Thêm array mới vào $_SESSION['cart']

7. TRẢ VỀ RESPONSE
   └── {
         success: true,
         message: "Đã thêm vào giỏ hàng",
         cartCount: 3  // Tổng số item trong giỏ
       }
```

**Code xử lý:**
```php
public function add() {
    $variantId = (int) $_POST['variant_id'];
    $quantity = (int) ($_POST['quantity'] ?? 1);
    
    // Lấy thông tin variant và sản phẩm
    $variant = $this->db->fetchOne(
        "SELECT pv.*, p.name, p.price, p.discount_price, p.thumbnail
         FROM product_variants pv
         JOIN products p ON pv.product_id = p.id
         WHERE pv.id = ?",
        [$variantId]
    );
    
    if (!$variant) {
        return $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    }
    
    if ($variant['stock_quantity'] < $quantity) {
        return $this->json(['success' => false, 'message' => 'Không đủ số lượng trong kho']);
    }
    
    // Lấy giỏ hàng hiện tại
    $cart = $_SESSION['cart'] ?? [];
    $found = false;
    
    // Kiểm tra đã có trong giỏ chưa
    foreach ($cart as &$item) {
        if ($item['variant_id'] == $variantId) {
            // Đã có → Cộng thêm số lượng
            $newQty = $item['quantity'] + $quantity;
            
            // Kiểm tra không vượt quá tồn kho
            if ($newQty > $variant['stock_quantity']) {
                return $this->json(['success' => false, 'message' => 'Vượt quá số lượng trong kho']);
            }
            
            $item['quantity'] = $newQty;
            $found = true;
            break;
        }
    }
    
    // Chưa có → Thêm mới
    if (!$found) {
        $cart[] = [
            'variant_id' => $variantId,
            'product_id' => $variant['product_id'],
            'product_name' => $variant['name'],
            'color' => $variant['color'],
            'size' => $variant['size'],
            'price' => $variant['discount_price'] ?: $variant['price'],
            'quantity' => $quantity,
            'thumbnail' => $variant['thumbnail']
        ];
    }
    
    $_SESSION['cart'] = $cart;
    
    return $this->json([
        'success' => true,
        'message' => 'Đã thêm vào giỏ hàng',
        'cartCount' => count($cart)
    ]);
}
```

---

## CHỨC NĂNG 2: XEM GIỎ HÀNG

**URL:** GET `/cart`

**Quy trình xử lý:**

```
1. LẤY GIỎ HÀNG TỪ SESSION
   └── $cart = $_SESSION['cart'] ?? []

2. KIỂM TRA VÀ CẬP NHẬT THÔNG TIN
   ├── Duyệt qua từng item
   ├── Kiểm tra variant còn tồn tại không (sản phẩm có thể bị xóa)
   ├── Kiểm tra tồn kho hiện tại
   └── Cập nhật giá mới nhất (giá có thể thay đổi)

3. TÍNH TỔNG TIỀN
   └── $total = Σ (price × quantity)

4. RENDER VIEW
   └── Hiển thị danh sách sản phẩm và tổng tiền
```

**Thông tin hiển thị mỗi item:**
- Ảnh thumbnail
- Tên sản phẩm
- Màu sắc
- Size
- Đơn giá (đã format: 2,990,000đ)
- Số lượng (input có nút +/-)
- Thành tiền (đơn giá × số lượng)
- Nút xóa

**Tổng kết:**
- Tạm tính: Tổng tiền tất cả sản phẩm
- Nút "Tiếp tục mua sắm"
- Nút "Thanh toán"

---

## CHỨC NĂNG 3: CẬP NHẬT SỐ LƯỢNG

**URL:** POST `/cart/update`

**Dữ liệu gửi lên:**
```
variant_id: 2
quantity: 3
```

**Quy trình xử lý:**

```
1. NHẬN DỮ LIỆU
   ├── variant_id: ID variant cần cập nhật
   └── quantity: Số lượng mới

2. VALIDATE SỐ LƯỢNG
   ├── quantity >= 1 (không được âm hoặc 0)
   └── quantity <= stock_quantity (không vượt tồn kho)

3. TÌM VÀ CẬP NHẬT TRONG SESSION
   └── Duyệt $_SESSION['cart'], tìm item có variant_id trùng
       → Cập nhật quantity

4. TÍNH LẠI TỔNG TIỀN
   └── $total = Σ (price × quantity)

5. TRẢ VỀ RESPONSE
   └── {
         success: true,
         itemTotal: 5980000,    // Thành tiền của item
         cartTotal: 9470000,    // Tổng tiền giỏ hàng
         cartCount: 3
       }
```

**Code xử lý:**
```php
public function update() {
    $variantId = (int) $_POST['variant_id'];
    $quantity = (int) $_POST['quantity'];
    
    if ($quantity < 1) {
        return $this->json(['success' => false, 'message' => 'Số lượng không hợp lệ']);
    }
    
    // Kiểm tra tồn kho
    $variant = $this->db->fetchOne(
        "SELECT stock_quantity FROM product_variants WHERE id = ?",
        [$variantId]
    );
    
    if (!$variant) {
        return $this->json(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    }
    
    if ($quantity > $variant['stock_quantity']) {
        return $this->json([
            'success' => false, 
            'message' => 'Chỉ còn ' . $variant['stock_quantity'] . ' sản phẩm'
        ]);
    }
    
    // Cập nhật số lượng
    $cart = $_SESSION['cart'] ?? [];
    $itemTotal = 0;
    $cartTotal = 0;
    
    foreach ($cart as &$item) {
        if ($item['variant_id'] == $variantId) {
            $item['quantity'] = $quantity;
            $itemTotal = $item['price'] * $quantity;
        }
        $cartTotal += $item['price'] * $item['quantity'];
    }
    
    $_SESSION['cart'] = $cart;
    
    return $this->json([
        'success' => true,
        'itemTotal' => $itemTotal,
        'cartTotal' => $cartTotal,
        'cartCount' => count($cart)
    ]);
}
```

---

## CHỨC NĂNG 4: XÓA SẢN PHẨM KHỎI GIỎ

**URL:** POST `/cart/remove`

**Dữ liệu gửi lên:**
```
variant_id: 2
```

**Quy trình xử lý:**

```
1. NHẬN variant_id CẦN XÓA

2. LỌC BỎ ITEM KHỎI GIỎ
   └── array_filter: Giữ lại các item có variant_id khác

3. CẬP NHẬT SESSION

4. TÍNH LẠI TỔNG TIỀN

5. TRẢ VỀ RESPONSE
   └── {
         success: true,
         message: "Đã xóa sản phẩm",
         cartTotal: 3500000,
         cartCount: 2
       }
```

**Code xử lý:**
```php
public function remove() {
    $variantId = (int) $_POST['variant_id'];
    
    $cart = $_SESSION['cart'] ?? [];
    
    // Lọc bỏ item cần xóa
    $cart = array_filter($cart, function($item) use ($variantId) {
        return $item['variant_id'] != $variantId;
    });
    
    // Reindex array
    $cart = array_values($cart);
    
    $_SESSION['cart'] = $cart;
    
    // Tính tổng tiền mới
    $cartTotal = 0;
    foreach ($cart as $item) {
        $cartTotal += $item['price'] * $item['quantity'];
    }
    
    return $this->json([
        'success' => true,
        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
        'cartTotal' => $cartTotal,
        'cartCount' => count($cart)
    ]);
}
```

---

## CHỨC NĂNG 5: XÓA TOÀN BỘ GIỎ HÀNG

**URL:** POST `/cart/clear`

**Quy trình xử lý:**
```php
public function clear() {
    unset($_SESSION['cart']);
    
    return $this->json([
        'success' => true,
        'message' => 'Đã xóa toàn bộ giỏ hàng'
    ]);
}
```

---

## CHỨC NĂNG 6: ĐẾM SỐ LƯỢNG TRONG GIỎ

**URL:** GET `/cart/count`

**Mục đích:** Hiển thị số lượng trên icon giỏ hàng ở header

**Code xử lý:**
```php
public function count() {
    $cart = $_SESSION['cart'] ?? [];
    $count = count($cart);
    
    return $this->json(['count' => $count]);
}
```

---

## XỬ LÝ TRÊN GIAO DIỆN (JAVASCRIPT)

**Thêm vào giỏ:**
```javascript
function addToCart(variantId, quantity) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `variant_id=${variantId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng trên icon giỏ hàng
            document.getElementById('cartCount').textContent = data.cartCount;
            // Hiển thị toast thông báo
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    });
}
```

**Cập nhật số lượng:**
```javascript
function updateQuantity(variantId, quantity) {
    fetch('/cart/update', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `variant_id=${variantId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Cập nhật thành tiền của item
            document.querySelector(`#item-${variantId} .item-total`).textContent = 
                formatMoney(data.itemTotal);
            // Cập nhật tổng tiền giỏ hàng
            document.getElementById('cartTotal').textContent = 
                formatMoney(data.cartTotal);
        } else {
            showToast(data.message, 'error');
        }
    });
}
```

**Xóa sản phẩm:**
```javascript
function removeItem(variantId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
    
    fetch('/cart/remove', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `variant_id=${variantId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Xóa row khỏi bảng
            document.getElementById(`item-${variantId}`).remove();
            // Cập nhật tổng tiền
            document.getElementById('cartTotal').textContent = 
                formatMoney(data.cartTotal);
            // Cập nhật icon
            document.getElementById('cartCount').textContent = data.cartCount;
            
            // Nếu giỏ rỗng → Hiển thị thông báo
            if (data.cartCount === 0) {
                showEmptyCart();
            }
        }
    });
}
```

---

## XỬ LÝ CÁC TRƯỜNG HỢP ĐẶC BIỆT

### 1. Sản phẩm bị xóa khỏi hệ thống
- Khi xem giỏ hàng, kiểm tra variant còn tồn tại không
- Nếu không → Tự động xóa khỏi giỏ, thông báo cho khách

### 2. Sản phẩm hết hàng
- Khi xem giỏ hàng, kiểm tra tồn kho
- Nếu hết hàng → Hiển thị "Hết hàng", disable nút thanh toán
- Nếu còn ít hơn số lượng trong giỏ → Thông báo và tự động giảm

### 3. Giá sản phẩm thay đổi
- Khi xem giỏ hàng, lấy giá mới nhất từ database
- Cập nhật lại giá trong session
- Thông báo cho khách nếu giá thay đổi

### 4. Giỏ hàng rỗng
- Hiển thị thông báo "Giỏ hàng trống"
- Nút "Tiếp tục mua sắm" để quay lại trang sản phẩm

---

## TỔNG KẾT CÁC ENDPOINT

| Method | URL | Chức năng |
|--------|-----|-----------|
| POST | /cart/add | Thêm sản phẩm vào giỏ |
| GET | /cart | Xem giỏ hàng |
| POST | /cart/update | Cập nhật số lượng |
| POST | /cart/remove | Xóa 1 sản phẩm |
| POST | /cart/clear | Xóa toàn bộ giỏ |
| GET | /cart/count | Đếm số lượng |
