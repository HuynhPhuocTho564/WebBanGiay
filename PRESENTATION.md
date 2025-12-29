# KỊCH BẢN DEMO - WEBSITE BÁN GIÀY SNEAKER STORE

---

## PHẦN 1: KIẾN TRÚC HỆ THỐNG

### 1.1. Công nghệ sử dụng

**Backend: PHP thuần (không framework)**
- Không sử dụng Laravel, CodeIgniter hay bất kỳ framework nào
- Tự xây dựng mô hình MVC từ đầu
- Mục đích: Hiểu sâu cách framework hoạt động bên trong

**Database: MySQL**
- Hệ quản trị CSDL quan hệ phổ biến nhất
- Sử dụng PDO (PHP Data Objects) để kết nối
- PDO hỗ trợ prepared statements chống SQL Injection

**Frontend: HTML5, CSS3, JavaScript, Tailwind CSS**
- Tailwind CSS: Framework CSS utility-first, viết style trực tiếp trong class HTML
- Không cần viết file CSS riêng, code gọn hơn Bootstrap

**API tích hợp: Google OAuth 2.0**
- Cho phép đăng nhập bằng tài khoản Google
- Không cần nhớ thêm mật khẩu mới
- Lấy được thông tin user (tên, email, avatar) từ Google

### 1.2. Mô hình MVC tự xây dựng

**MVC là gì?**
- Model: Xử lý dữ liệu, tương tác database
- View: Giao diện hiển thị cho người dùng
- Controller: Điều khiển luồng, nhận request, gọi Model, trả về View

**Tại sao dùng MVC?**
- Tách biệt code: Logic riêng, giao diện riêng, dễ bảo trì
- Nhiều người làm việc cùng lúc không conflict
- Dễ mở rộng, thay đổi giao diện không ảnh hưởng logic

**Cấu trúc thư mục:**
```
shop_giay_project/
├── app/
│   ├── core/           # Lõi hệ thống
│   │   ├── App.php         # Router - Điều hướng URL
│   │   ├── Controller.php  # Base Controller - Class cha
│   │   ├── Database.php    # Kết nối và thao tác MySQL
│   │   ├── Session.php     # Quản lý phiên đăng nhập
│   │   └── Middleware.php  # Kiểm tra quyền truy cập
│   │
│   ├── controllers/    # Xử lý logic nghiệp vụ
│   │   ├── HomeController.php      # Trang chủ, sản phẩm
│   │   ├── AuthController.php      # Đăng nhập, đăng ký
│   │   ├── CartController.php      # Giỏ hàng
│   │   ├── CheckoutController.php  # Thanh toán
│   │   ├── ProfileController.php   # Tài khoản user
│   │   ├── AdminController.php     # Dashboard admin
│   │   ├── AdminProductController.php   # Quản lý sản phẩm
│   │   ├── AdminOrderController.php     # Quản lý đơn hàng
│   │   ├── AdminUserController.php      # Quản lý user
│   │   └── ...
│   │
│   ├── models/         # Tương tác database
│   │   ├── ProductModel.php  # Truy vấn bảng products
│   │   └── UserModel.php     # Truy vấn bảng users
│   │
│   └── helpers/        # Hàm tiện ích dùng chung
│       └── functions.php     # formatMoney(), productImage()...
│
├── views/              # Giao diện HTML
│   ├── layouts/        # Header, Footer dùng chung
│   ├── client/         # Trang khách hàng
│   ├── admin/          # Trang quản trị
│   └── components/     # Component tái sử dụng
│
├── config/             # Cấu hình
│   ├── config.php          # DB, BASE_URL, constants
│   └── google_config.php   # Google OAuth credentials
│
├── public/             # Thư mục public (web root)
│   ├── assets/         # CSS, JS, Images
│   └── uploads/        # File upload (ảnh sản phẩm, avatar)
│
└── database/           # SQL scripts
    └── schema.sql      # Tạo database và dữ liệu mẫu
```

**Luồng xử lý request chi tiết:**

```
BƯỚC 1: User gõ URL
─────────────────────────────────────────────────────────
Browser: http://localhost/shop_giay_project/public/home/product/nike-air-max

BƯỚC 2: .htaccess rewrite URL
─────────────────────────────────────────────────────────
File .htaccess chuyển URL thành:
→ index.php?url=home/product/nike-air-max

Giải thích: .htaccess là file cấu hình Apache, dùng mod_rewrite
để chuyển URL đẹp thành query string cho PHP xử lý

BƯỚC 3: index.php khởi tạo
─────────────────────────────────────────────────────────
- Load file config/config.php (cấu hình DB, constants)
- Load file app/helpers/functions.php (hàm tiện ích)
- Khởi tạo Session
- new App() → Bắt đầu routing

BƯỚC 4: App.php parse URL
─────────────────────────────────────────────────────────
$url = $_GET['url'] = "home/product/nike-air-max"
$url = explode('/', $url) = ['home', 'product', 'nike-air-max']

Xác định:
- Controller: $url[0] = 'home' → HomeController
- Method: $url[1] = 'product' → method product()
- Params: $url[2] = 'nike-air-max' → tham số truyền vào

BƯỚC 5: Load Controller và gọi Method
─────────────────────────────────────────────────────────
require_once 'app/controllers/HomeController.php';
$controller = new HomeController();
$controller->product('nike-air-max');

BƯỚC 6: Controller xử lý
─────────────────────────────────────────────────────────
class HomeController {
    public function product($slug) {
        // 1. Gọi Model lấy dữ liệu
        $product = $this->productModel->findBySlug($slug);
        
        // 2. Lấy thêm variants, gallery, reviews
        $variants = $this->productModel->getVariants($product['id']);
        
        // 3. Render View với dữ liệu
        $this->view('client/product-detail', [
            'product' => $product,
            'variants' => $variants
        ]);
    }
}

BƯỚC 7: View render HTML
─────────────────────────────────────────────────────────
File views/client/product-detail.php nhận $product, $variants
và render thành HTML trả về cho browser
```

### 2.3. Chi tiết sản phẩm - Chọn Variant (Size/Màu)

**URL:** `/home/product/{slug}`

**Luồng chọn variant trên giao diện:**

```
BƯỚC 1: Load trang chi tiết sản phẩm
─────────────────────────────────────────────────────────
- Hiển thị thông tin sản phẩm: Tên, giá, mô tả, ảnh
- Load tất cả variants từ database
- JavaScript nhận mảng variants dạng JSON

Ví dụ mảng variants:
[
    {id: 1, color: "Đen", size: "40", stock_quantity: 5},
    {id: 2, color: "Đen", size: "41", stock_quantity: 3},
    {id: 3, color: "Đen", size: "42", stock_quantity: 0},
    {id: 4, color: "Trắng", size: "40", stock_quantity: 8},
    {id: 5, color: "Trắng", size: "41", stock_quantity: 6}
]

BƯỚC 2: Hiển thị các màu có sẵn
─────────────────────────────────────────────────────────
- Lấy unique colors từ mảng variants
- Render radio buttons: [Đen] [Trắng]

BƯỚC 3: User click chọn màu "Đen"
─────────────────────────────────────────────────────────
- selectedColor = "Đen"
- Filter variants có color = "Đen"
- Lấy các size: 40, 41, 42
- Render radio buttons cho size
- Size 42 có stock = 0 → Disable, hiện "Hết hàng"

BƯỚC 4: User click chọn size "41"
─────────────────────────────────────────────────────────
- selectedSize = "41"
- Tìm variant: color="Đen" AND size="41"
- Tìm thấy: {id: 2, stock_quantity: 3}
- Hiển thị: "Còn 3 sản phẩm"
- Enable nút "Thêm vào giỏ"

BƯỚC 5: User click "Thêm vào giỏ"
─────────────────────────────────────────────────────────
- Gửi AJAX POST đến /cart/add
- Body: {variant_id: 2, quantity: 1}
- Server thêm vào session cart
- Response: {success: true, cartCount: 3}
- Cập nhật icon giỏ hàng, hiện toast thông báo
```

**Code JavaScript xử lý chọn variant:**

```javascript
// Mảng variants từ PHP
const variants = <?= json_encode($variants) ?>;
let selectedVariant = null;

function updateVariant() {
    const color = document.querySelector('input[name="color"]:checked')?.value;
    const size = document.querySelector('input[name="size"]:checked')?.value;
    
    if (color && size) {
        // Tìm variant khớp màu và size
        selectedVariant = variants.find(v => v.color === color && v.size === size);
        
        if (selectedVariant) {
            if (selectedVariant.stock_quantity > 0) {
                // Còn hàng
                document.getElementById('stockInfo').innerHTML = 
                    `Còn ${selectedVariant.stock_quantity} sản phẩm`;
                document.getElementById('addToCartBtn').disabled = false;
            } else {
                // Hết hàng
                document.getElementById('stockInfo').innerHTML = 'Hết hàng';
                document.getElementById('addToCartBtn').disabled = true;
            }
        }
    }
}
```

### 2.4. Giỏ hàng (Cart)

**Cách lưu giỏ hàng:**
- Lưu trong SESSION (không cần đăng nhập vẫn thêm được)
- Khi đăng nhập, có thể sync với database

**Cấu trúc session cart:**
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
    [
        'variant_id' => 5,
        'product_id' => 1,
        'product_name' => 'Nike Air Max 90',
        'color' => 'Trắng',
        'size' => '41',
        'price' => 2990000,
        'quantity' => 1,
        'thumbnail' => 'nike-air-max.jpg'
    ]
];
```

**Các thao tác với giỏ hàng:**

```php
// Thêm sản phẩm
public function add() {
    $variantId = $_POST['variant_id'];
    $quantity = $_POST['quantity'] ?? 1;
    
    // Kiểm tra variant tồn tại và còn hàng
    $variant = $this->db->fetchOne(
        "SELECT pv.*, p.name, p.price, p.discount_price, p.thumbnail
         FROM product_variants pv
         JOIN products p ON pv.product_id = p.id
         WHERE pv.id = ?", 
        [$variantId]
    );
    
    if (!$variant || $variant['stock_quantity'] < $quantity) {
        return json(['success' => false, 'message' => 'Không đủ hàng']);
    }
    
    // Kiểm tra đã có trong giỏ chưa
    $cart = $_SESSION['cart'] ?? [];
    $found = false;
    
    foreach ($cart as &$item) {
        if ($item['variant_id'] == $variantId) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
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
    return json(['success' => true, 'cartCount' => count($cart)]);
}

// Cập nhật số lượng
public function update() {
    $variantId = $_POST['variant_id'];
    $quantity = $_POST['quantity'];
    
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['variant_id'] == $variantId) {
            $item['quantity'] = $quantity;
            break;
        }
    }
}

// Xóa sản phẩm
public function remove() {
    $variantId = $_POST['variant_id'];
    
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($variantId) {
        return $item['variant_id'] != $variantId;
    });
}
```

### 2.5. Thanh toán (Checkout) - Xử lý đặt hàng với Transaction

**Tại sao cần Transaction?**

Khi đặt hàng, cần thực hiện nhiều thao tác:
1. Kiểm tra tồn kho
2. Trừ số lượng trong kho
3. Tạo đơn hàng
4. Tạo chi tiết đơn hàng
5. Cập nhật coupon (nếu có)
6. Xóa giỏ hàng

Nếu bước 3 thành công nhưng bước 4 lỗi → Dữ liệu không nhất quán!
Transaction đảm bảo: Hoặc TẤT CẢ thành công, hoặc TẤT CẢ rollback.

**Race Condition là gì?**

Tình huống: Sản phẩm còn 1 đôi, 2 người đặt cùng lúc
- User A: Kiểm tra → còn 1 → OK
- User B: Kiểm tra → còn 1 → OK
- User A: Trừ kho → còn 0
- User B: Trừ kho → còn -1 ← SAI!

**Giải pháp: SELECT ... FOR UPDATE**

```sql
-- Lock row để user khác phải đợi
SELECT stock_quantity FROM product_variants 
WHERE id = 2 FOR UPDATE;
```

**Code xử lý đặt hàng:**

```php
public function placeOrder() {
    // Bắt đầu transaction
    $this->db->beginTransaction();
    
    try {
        $cart = $_SESSION['cart'];
        $userId = Session::userId();
        
        // 1. Kiểm tra và trừ tồn kho từng item
        foreach ($cart as $item) {
            // Lock row với FOR UPDATE
            $variant = $this->db->fetchOne(
                "SELECT stock_quantity FROM product_variants 
                 WHERE id = ? FOR UPDATE",
                [$item['variant_id']]
            );
            
            // Kiểm tra còn đủ hàng không
            if ($variant['stock_quantity'] < $item['quantity']) {
                throw new Exception("Sản phẩm {$item['product_name']} không đủ số lượng");
            }
            
            // Trừ tồn kho
            $this->db->query(
                "UPDATE product_variants 
                 SET stock_quantity = stock_quantity - ? 
                 WHERE id = ?",
                [$item['quantity'], $item['variant_id']]
            );
        }
        
        // 2. Tính tổng tiền
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // 3. Áp dụng coupon (nếu có)
        $discount = 0;
        $couponId = $_SESSION['applied_coupon'] ?? null;
        if ($couponId) {
            $coupon = $this->db->fetchOne(
                "SELECT * FROM coupons WHERE id = ? AND status = 1 
                 AND start_date <= NOW() AND end_date >= NOW()
                 AND (max_uses IS NULL OR used_count < max_uses)",
                [$couponId]
            );
            
            if ($coupon) {
                if ($coupon['discount_type'] == 'percent') {
                    $discount = $subtotal * $coupon['discount_value'] / 100;
                    if ($coupon['max_discount']) {
                        $discount = min($discount, $coupon['max_discount']);
                    }
                } else {
                    $discount = $coupon['discount_value'];
                }
                
                // Tăng số lần sử dụng coupon
                $this->db->query(
                    "UPDATE coupons SET used_count = used_count + 1 WHERE id = ?",
                    [$couponId]
                );
            }
        }
        
        $total = $subtotal - $discount;
        
        // 4. Tạo đơn hàng
        $orderId = $this->db->insert('orders', [
            'user_id' => $userId,
            'order_code' => 'ORD' . time() . rand(100, 999),
            'fullname' => $_POST['fullname'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'note' => $_POST['note'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon_id' => $couponId,
            'total' => $total,
            'payment_method' => $_POST['payment_method'],
            'status' => 'pending'
        ]);
        
        // 5. Tạo chi tiết đơn hàng
        foreach ($cart as $item) {
            $this->db->insert('order_items', [
                'order_id' => $orderId,
                'variant_id' => $item['variant_id'],
                'product_name' => $item['product_name'],
                'color' => $item['color'],
                'size' => $item['size'],
                'price' => $item['price'],
                'quantity' => $item['quantity']
            ]);
        }
        
        // 6. Commit transaction
        $this->db->commit();
        
        // 7. Xóa giỏ hàng
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);
        
        // Redirect trang thành công
        Session::flash('success', 'Đặt hàng thành công!');
        $this->redirect('checkout/success/' . $orderId);
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $this->db->rollback();
        Session::flash('error', $e->getMessage());
        $this->redirect('checkout');
    }
}
```

**Giải thích từng bước:**

1. `beginTransaction()`: Bắt đầu transaction, các thay đổi chưa được lưu vĩnh viễn
2. `FOR UPDATE`: Lock các row đang đọc, user khác phải đợi
3. Kiểm tra tồn kho: Nếu không đủ → throw Exception → rollback
4. Trừ tồn kho: `stock_quantity = stock_quantity - quantity`
5. Tạo order và order_items
6. `commit()`: Lưu vĩnh viễn tất cả thay đổi
7. Nếu có lỗi ở bất kỳ bước nào → `rollback()` → Hoàn tác tất cả

---

## PHẦN 3: DEMO CHỨC NĂNG QUẢN TRỊ (ADMIN)

### 3.1. Hệ thống phân quyền (Authorization)

**3 cấp quyền trong hệ thống:**

| Role | Giá trị | Mô tả | Quyền hạn |
|------|---------|-------|-----------|
| Customer | 0 | Khách hàng | Chỉ truy cập trang client, mua hàng |
| Staff | 1 | Nhân viên | Truy cập admin, quản lý đơn/sản phẩm, KHÔNG xem báo cáo doanh thu, KHÔNG xóa sản phẩm |
| Admin | 2 | Quản trị viên | Toàn quyền |

**Cách lưu role trong database:**

```sql
CREATE TABLE tblUser (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    fullname VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    role TINYINT DEFAULT 0,      -- 0: Customer, 1: Staff, 2: Admin
    status TINYINT DEFAULT 1,    -- 1: Active, 0: Blocked
    created_at TIMESTAMP
);
```

**Cách kiểm tra quyền trong code:**

```php
// Session.php - Định nghĩa constants
class Session {
    const ROLE_CUSTOMER = 0;
    const ROLE_STAFF = 1;
    const ROLE_ADMIN = 2;
    
    public static function role() {
        return $_SESSION['user_role'] ?? self::ROLE_CUSTOMER;
    }
    
    public static function isAdmin() {
        return self::role() >= self::ROLE_ADMIN;
    }
    
    public static function isStaff() {
        return self::role() >= self::ROLE_STAFF;
    }
}

// Middleware.php - Kiểm tra quyền trước khi vào controller
class Middleware {
    // Yêu cầu đăng nhập
    public static function auth() {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Vui lòng đăng nhập');
            redirect('auth/login');
            exit;
        }
    }
    
    // Yêu cầu là Staff hoặc Admin
    public static function admin() {
        self::auth();
        if (Session::role() < Session::ROLE_STAFF) {
            Session::flash('error', 'Bạn không có quyền truy cập');
            redirect('/');
            exit;
        }
    }
    
    // Yêu cầu là Admin (Staff không được)
    public static function adminOnly() {
        self::auth();
        if (Session::role() < Session::ROLE_ADMIN) {
            Session::flash('error', 'Chỉ Admin mới có quyền');
            redirect('admin');
            exit;
        }
    }
}

// Sử dụng trong Controller
class AdminReportController extends Controller {
    public function __construct() {
        Middleware::adminOnly(); // Chỉ Admin mới vào được
    }
}

class AdminProductController extends Controller {
    public function __construct() {
        Middleware::admin(); // Staff và Admin đều vào được
    }
    
    public function delete($id) {
        // Kiểm tra thêm: Staff không được xóa
        if (Session::role() < Session::ROLE_ADMIN) {
            return $this->json(['success' => false, 'message' => 'Không có quyền xóa']);
        }
        // ... xóa sản phẩm
    }
}
```

**Ẩn/hiện menu theo quyền trong View:**

```php
<!-- Sidebar Admin -->
<nav>
    <a href="/admin">Dashboard</a>
    <a href="/adminProduct">Sản phẩm</a>
    <a href="/adminOrder">Đơn hàng</a>
    <a href="/adminUser">Người dùng</a>
    
    <?php if (Session::isAdmin()): ?>
    <!-- Chỉ Admin thấy menu này -->
    <a href="/adminReport">Báo cáo doanh thu</a>
    <?php endif; ?>
</nav>
```

### 3.2. Khóa tài khoản vi phạm

**Cách hoạt động:**

```php
// AdminUserController.php
public function block($userId) {
    // Cập nhật status = 0 (blocked)
    $this->db->update('tblUser', 
        ['status' => 0], 
        'id = ?', 
        [$userId]
    );
    
    Session::flash('success', 'Đã khóa tài khoản');
    $this->redirect('adminUser');
}

public function unblock($userId) {
    // Cập nhật status = 1 (active)
    $this->db->update('tblUser', 
        ['status' => 1], 
        'id = ?', 
        [$userId]
    );
    
    Session::flash('success', 'Đã mở khóa tài khoản');
    $this->redirect('adminUser');
}

// AuthController.php - Kiểm tra khi đăng nhập
public function login() {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $user = $this->db->fetchOne(
        "SELECT * FROM tblUser WHERE email = ?", 
        [$email]
    );
    
    if (!$user) {
        return $this->error('Email không tồn tại');
    }
    
    if (!password_verify($password, $user['password'])) {
        return $this->error('Mật khẩu không đúng');
    }
    
    // KIỂM TRA TÀI KHOẢN BỊ KHÓA
    if ($user['status'] == 0) {
        return $this->error('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin.');
    }
    
    // Đăng nhập thành công
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['fullname'];
    
    // Regenerate session ID để chống session fixation
    session_regenerate_id(true);
    
    $this->redirect('/');
}
```

### 3.3. Quản lý đơn hàng - Cập nhật trạng thái

**Các trạng thái đơn hàng:**

```
pending (Chờ xác nhận)
    ↓ Admin click "Xác nhận"
confirmed (Đã xác nhận)
    ↓ Admin click "Giao hàng"
shipping (Đang giao)
    ↓ Admin click "Hoàn thành"
completed (Hoàn thành)

Hoặc:
pending → cancelled (Hủy đơn)
completed → return_requested (Yêu cầu trả hàng) → returned (Đã trả)
```

**Xử lý hủy đơn - Hoàn lại tồn kho:**

```php
public function cancel($orderId) {
    $this->db->beginTransaction();
    
    try {
        // Lấy thông tin đơn hàng
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ?", 
            [$orderId]
        );
        
        // Chỉ hủy được đơn chờ xác nhận hoặc đã xác nhận
        if (!in_array($order['status'], ['pending', 'confirmed'])) {
            throw new Exception('Không thể hủy đơn hàng này');
        }
        
        // Lấy chi tiết đơn hàng
        $items = $this->db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?", 
            [$orderId]
        );
        
        // Hoàn lại tồn kho cho từng item
        foreach ($items as $item) {
            $this->db->query(
                "UPDATE product_variants 
                 SET stock_quantity = stock_quantity + ? 
                 WHERE id = ?",
                [$item['quantity'], $item['variant_id']]
            );
        }
        
        // Cập nhật trạng thái đơn hàng
        $this->db->update('orders', 
            ['status' => 'cancelled'], 
            'id = ?', 
            [$orderId]
        );
        
        $this->db->commit();
        Session::flash('success', 'Đã hủy đơn hàng và hoàn lại tồn kho');
        
    } catch (Exception $e) {
        $this->db->rollback();
        Session::flash('error', $e->getMessage());
    }
    
    $this->redirect('adminOrder/detail/' . $orderId);
}
```

---

## PHẦN 4: BẢO MẬT WEBSITE

### 4.1. CSRF Protection (Cross-Site Request Forgery)

**CSRF là gì?**
- Tấn công giả mạo request từ site khác
- Ví dụ: Hacker tạo form ẩn trên site của họ, khi user click → gửi request đến website của bạn
- Nếu user đang đăng nhập → Request được thực hiện với quyền của user đó

**Cách phòng chống:**

```php
// Session.php - Tạo CSRF token
public static function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

public static function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// helpers/functions.php - Helper tạo hidden field
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . Session::generateCsrfToken() . '">';
}

// Trong View - Thêm vào mọi form POST
<form method="POST" action="/checkout/placeOrder">
    <?= csrfField() ?>
    <!-- các field khác -->
</form>

// Trong Controller - Kiểm tra token
public function placeOrder() {
    if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }
    // ... xử lý tiếp
}
```

### 4.2. SQL Injection Prevention

**SQL Injection là gì?**
- Hacker chèn code SQL vào input
- Ví dụ: Nhập email = `' OR '1'='1` → Bypass đăng nhập

**Cách SAI (dễ bị tấn công):**
```php
// KHÔNG BAO GIỜ LÀM THẾ NÀY!
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
// Nếu email = "' OR '1'='1" → SELECT * FROM users WHERE email = '' OR '1'='1'
// → Trả về tất cả users!
```

**Cách ĐÚNG (Prepared Statements):**
```php
// Database.php
public function fetchOne($sql, $params = []) {
    $stmt = $this->pdo->prepare($sql);  // Chuẩn bị câu query
    $stmt->execute($params);             // Truyền params riêng
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Sử dụng
$email = $_POST['email'];
$user = $this->db->fetchOne(
    "SELECT * FROM users WHERE email = ?",  // ? là placeholder
    [$email]                                  // Giá trị truyền riêng
);
// PDO tự động escape, không thể inject
```

### 4.3. XSS Prevention (Cross-Site Scripting)

**XSS là gì?**
- Hacker chèn JavaScript vào website
- Ví dụ: Đặt tên sản phẩm = `<script>alert('hacked')</script>`
- Khi hiển thị → Script chạy trên browser của user khác

**Cách phòng chống:**
```php
// KHÔNG BAO GIỜ echo trực tiếp user input
echo $product['name']; // NGUY HIỂM!

// LUÔN dùng htmlspecialchars()
echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
// Chuyển < thành &lt;, > thành &gt;
// Script không thể chạy được
```

### 4.4. Password Hashing

**Tại sao không lưu password plaintext?**
- Nếu database bị hack → Lộ tất cả mật khẩu
- User thường dùng chung password cho nhiều site

**Cách hash password:**
```php
// Khi đăng ký
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
// Kết quả: $2y$10$abcdef... (60 ký tự, không thể đảo ngược)

// Khi đăng nhập
$inputPassword = $_POST['password'];
$storedHash = $user['password']; // Lấy từ database

if (password_verify($inputPassword, $storedHash)) {
    // Đúng mật khẩu
} else {
    // Sai mật khẩu
}
```

### 4.5. Session Security

```php
// Regenerate session ID khi đăng nhập (chống session fixation)
session_regenerate_id(true);

// Session timeout 30 phút
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    redirect('auth/login');
}
$_SESSION['last_activity'] = time();

// Chỉ truyền session qua HTTPS (production)
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
```

---

## PHẦN 5: CÂU HỎI THƯỜNG GẶP KHI BẢO VỆ

### Q1: "Tại sao không dùng Laravel/CodeIgniter?"
**Trả lời:** Em chọn PHP thuần để hiểu sâu cách MVC hoạt động. Sau khi tự xây dựng Router, Controller, Database wrapper, em hiểu rõ hơn cách các framework làm việc bên trong. Nếu làm dự án thực tế, em sẽ dùng Laravel để tiết kiệm thời gian và có nhiều tính năng sẵn.

### Q2: "Xử lý thế nào khi 2 người đặt cùng sản phẩm cùng lúc?"
**Trả lời:** Em sử dụng Database Transaction kết hợp với `SELECT ... FOR UPDATE` để lock row. Khi user A đang xử lý đơn hàng, row tồn kho bị lock, user B phải đợi. Sau khi A commit, B mới được đọc và thấy số lượng đã giảm. Nếu hết hàng, B sẽ nhận thông báo lỗi và rollback.

### Q3: "Tại sao tách bảng product_variants?"
**Trả lời:** Vì một sản phẩm giày có nhiều biến thể (size + màu), mỗi biến thể có số lượng tồn kho riêng. Nếu lưu chung vào bảng products, không thể quản lý được "Nike Air Max size 40 màu đen còn 5 đôi, size 41 màu trắng còn 8 đôi". Cách thiết kế này giống các sàn TMĐT lớn như Shopee, Tiki.

### Q4: "Website có những biện pháp bảo mật nào?"
**Trả lời:**
- CSRF Token cho mọi form POST
- Prepared Statements chống SQL Injection
- htmlspecialchars() chống XSS
- Password hash bằng bcrypt
- Session regeneration khi login
- Session timeout 30 phút

### Q5: "Giải thích luồng đặt hàng?"
**Trả lời:**
1. User chọn sản phẩm, màu, size → Thêm vào giỏ (lưu session)
2. Vào trang checkout → Điền thông tin giao hàng
3. Click đặt hàng → Server bắt đầu transaction
4. Kiểm tra và trừ tồn kho từng variant (có lock row)
5. Tạo đơn hàng và chi tiết đơn
6. Commit transaction → Xóa giỏ hàng
7. Nếu có lỗi → Rollback, không mất dữ liệu

### Q6: "Phân quyền hoạt động như thế nào?"
**Trả lời:** Có 3 role: Customer (0), Staff (1), Admin (2). Khi đăng nhập, role được lưu vào session. Middleware kiểm tra role trước khi cho phép truy cập controller. Staff có thể vào admin nhưng không xem được báo cáo doanh thu và không xóa được sản phẩm.

---

## CHECKLIST TRƯỚC KHI BẢO VỆ

- [ ] Website chạy không lỗi
- [ ] Có tài khoản Google để demo đăng nhập
- [ ] Tài khoản Admin: admin@gmail.com / 123456
- [ ] Tài khoản Staff để demo phân quyền
- [ ] Tài khoản Customer để demo khóa tài khoản
- [ ] Có sản phẩm với nhiều variants (màu/size)
- [ ] Có mã giảm giá còn hiệu lực
- [ ] phpMyAdmin mở sẵn để show database
- [ ] IDE mở sẵn để show code khi cần
- [ ] Internet ổn định (cho Google Login)
- [ ] Đọc kỹ phần câu hỏi thường gặp
