# TÀI LIỆU CẤU TRÚC DỰ ÁN - SNEAKER STORE

## 📁 CẤU TRÚC THƯ MỤC

```
shop_giay_project/
├── app/                    # Logicpoint - Điểm vào duy nhất
├── .htaccess              # Cấu hình URL rewrite
├── config/                # Cấu hình
├── app/                   # Logic ứng dụng (MVC)
├── views/                 # Giao diện
├── public/                # Tài nguyên công khai
├── database/              # File SQL
└── vendor/                # Thư viện bên thứ 3
```

---

## 🚀 LUỒNG HOẠT ĐỘNG

```
Request → index.php → App.php → Controller → Model → View → Response
```

**Ví dụ:** Truy cập `http://localhost/shop_giay_project/public/home/products`

1. `.htaccess` chuyển request đến `index.php`
2. `index.php` load config và khởi tạo `App`
3. `App.php` phân tích URL: controller=`home`, method=`products`
4. Gọi `HomeController->products()`
5. Controller lấy dữ liệu từ `ProductModel`
6. Controller render view `client/products.php`
7. Trả về HTML cho trình duyệt

---

## 📂 CHI TIẾT TỪNG THƯ MỤC

### 1. `index.php` - Entry Point
```php
// Điểm vào duy nhất của ứng dụng
require_once 'config/config.php';  // Load cấu hình
require_once 'app/core/App.php';   // Load core
$app = new App();                   // Khởi chạy ứng dụng
```

### 2. `config/` - Cấu hình

| File | Mô tả |
|------|-------|
| `config.php` | Cấu hình chính: DB, URL, hằng số |
| `google_config.php` | Cấu hình đăng nhập Google OAuth |

**config.php hoạt động:**
```php
// Định nghĩa hằng số
define('DB_HOST', 'localhost');
define('DB_NAME', 'shop_giay_db');
define('BASE_URL', 'http://localhost/shop_giay_project/public');

// Autoload classes
spl_autoload_register(function($class) {
    // Tự động load class khi được gọi
});
```

---

### 3. `app/` - Logic ứng dụng (MVC Pattern)

#### 3.1 `app/core/` - Lõi hệ thống

| File | Chức năng |
|------|-----------|
| `App.php` | Router - Phân tích URL, gọi Controller |
| `Controller.php` | Base Controller - Class cha cho tất cả controller |
| `Database.php` | Singleton kết nối MySQL |
| `Session.php` | Quản lý session, đăng nhập |
| `Middleware.php` | Kiểm tra quyền truy cập |

**App.php - Router:**
```php
class App {
    public function __construct() {
        // URL: home/products/123
        // Phân tích thành:
        $controller = 'HomeController';  // home
        $method = 'products';            // products  
        $params = [123];                 // 123
        
        // Gọi controller
        $controller->$method($params);
    }
}
```

**Database.php - Singleton Pattern:**
```php
class Database {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Các method: query(), fetchOne(), fetchAll(), insert(), update()
}
```

**Session.php - Quản lý phiên:**
```php
class Session {
    public static function login($user);     // Lưu user vào session
    public static function logout();          // Xóa session
    public static function isLoggedIn();      // Kiểm tra đăng nhập
    public static function user();            // Lấy thông tin user
    public static function flash($type, $msg); // Thông báo toast
}
```

#### 3.2 `app/controllers/` - Xử lý logic

| Controller | Chức năng |
|------------|-----------|
| `HomeController` | Trang chủ, sản phẩm, tìm kiếm |
| `AuthController` | Đăng nhập, đăng ký, đăng xuất |
| `CartController` | Giỏ hàng |
| `CheckoutController` | Thanh toán |
| `ProfileController` | Tài khoản người dùng |
| `AdminController` | Dashboard admin |
| `AdminProductController` | Quản lý sản phẩm |
| `AdminOrderController` | Quản lý đơn hàng |
| `AdminUserController` | Quản lý người dùng |
| `AdminCategoryController` | Quản lý danh mục |
| `AdminBrandController` | Quản lý thương hiệu |
| `AdminCouponController` | Quản lý mã giảm giá |
| `AdminReportController` | Báo cáo thống kê |

**Cấu trúc Controller:**
```php
class HomeController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->productModel = $this->model('ProductModel');
    }
    
    // URL: /home/products
    public function products() {
        $products = $this->productModel->getProducts();
        
        $this->view('layouts/header', $data);
        $this->view('client/products', ['products' => $products]);
        $this->view('layouts/footer');
    }
}
```

#### 3.3 `app/models/` - Tương tác Database

| Model | Bảng | Chức năng |
|-------|------|-----------|
| `ProductModel` | products, variants, categories, brands | CRUD sản phẩm |
| `UserModel` | tblUser | CRUD người dùng |

**Cấu trúc Model:**
```php
class ProductModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getProducts($filters, $limit, $offset) {
        return $this->db->fetchAll("SELECT * FROM products...", $params);
    }
    
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
    }
}
```

#### 3.4 `app/helpers/` - Hàm tiện ích

**functions.php:**
```php
formatMoney($amount)      // 1500000 → "1.500.000₫"
discountPercent($old, $new) // Tính % giảm giá
avatar($path)             // Trả về URL avatar hoặc ảnh mặc định
productImage($path)       // Trả về URL ảnh sản phẩm
orderStatus($status)      // 'pending' → ['label' => 'Chờ xác nhận', 'color' => 'yellow']
createSlug($str)          // "Giày Nike" → "giay-nike"
```

---

### 4. `views/` - Giao diện (View)

```
views/
├── layouts/           # Layout chung
│   ├── header.php     # Header + Menu
│   └── footer.php     # Footer + Scripts
├── components/        # Component tái sử dụng
│   └── product-card.php
├── client/            # Giao diện khách hàng
│   ├── home.php
│   ├── products.php
│   ├── product-detail.php
│   ├── auth/          # Đăng nhập, đăng ký
│   ├── cart/          # Giỏ hàng
│   ├── checkout/      # Thanh toán
│   ├── profile/       # Tài khoản
│   └── pages/         # Trang tĩnh (hướng dẫn, chính sách)
└── admin/             # Giao diện quản trị
    ├── layouts/       # Header, footer admin
    ├── dashboard/
    ├── products/
    ├── orders/
    ├── users/
    ├── categories/
    ├── brands/
    ├── coupons/
    └── reports/
```

**Cách render view:**
```php
// Trong Controller
$this->view('layouts/header', ['pageTitle' => 'Trang chủ']);
$this->view('client/home', ['products' => $products]);
$this->view('layouts/footer');

// View nhận biến từ $data
// Trong home.php có thể dùng: $products, $pageTitle
```

---

### 5. `public/` - Tài nguyên công khai

```
public/
├── index.php          # Entry point thực sự
├── .htaccess          # URL rewrite
├── assets/
│   ├── css/style.css  # CSS tùy chỉnh
│   ├── js/            # JavaScript
│   ├── images/        # Ảnh tĩnh
│   └── fonts/         # Font chữ
└── uploads/           # File upload
    ├── avatars/       # Ảnh đại diện
    └── products/      # Ảnh sản phẩm
```

**public/.htaccess:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Chuyển tất cả request đến index.php
# VD: /home/products → index.php?url=home/products
```

---

### 6. `database/` - File SQL

| File | Mô tả |
|------|-------|
| `sample_data.sql` | Cấu trúc bảng + dữ liệu mẫu |
| `add_brand_status.sql` | Migration thêm cột status cho brands |
| `update_images.sql` | Cập nhật đường dẫn ảnh |
| `fix_avatar.sql` | Sửa lỗi avatar |

---

## 🔄 VÍ DỤ LUỒNG XỬ LÝ

### Đăng nhập
```
1. User truy cập /auth/login
2. App.php → AuthController->login()
3. Hiển thị form đăng nhập

4. User submit form (POST)
5. AuthController->login() xử lý POST
6. UserModel->findByUsername($username)
7. Kiểm tra password_verify()
8. Session::login($user)
9. Redirect về trang chủ
```

### Thêm vào giỏ hàng
```
1. User click "Thêm vào giỏ" (AJAX)
2. POST /cart/add {variant_id, quantity}
3. CartController->add()
4. Kiểm tra Session::isLoggedIn()
5. Lưu vào $_SESSION['cart']
6. Trả về JSON {success, cartCount}
7. JavaScript cập nhật icon giỏ hàng
```

### Đặt hàng
```
1. User vào /checkout
2. CheckoutController->index()
3. Lấy cart từ session
4. Hiển thị form thanh toán

5. User submit form
6. CheckoutController->placeOrder()
7. Validate thông tin
8. INSERT vào bảng orders
9. INSERT vào bảng order_details
10. UPDATE stock trong product_variants
11. Xóa cart khỏi session
12. Redirect /checkout/success/{order_id}
```

---

## 🔐 PHÂN QUYỀN

```php
// Middleware.php
class Middleware {
    public static function requireLogin() {
        if (!Session::isLoggedIn()) {
            redirect('auth/login');
        }
    }
    
    public static function requireAdmin() {
        if (Session::role() < 1) {
            redirect('');
        }
    }
}

// Sử dụng trong Controller
class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        Middleware::requireAdmin(); // Chặn nếu không phải admin
    }
}
```

---

## 📝 QUY ƯỚC ĐẶT TÊN

| Loại | Quy ước | Ví dụ |
|------|---------|-------|
| Controller | PascalCase + Controller | `HomeController`, `AdminProductController` |
| Model | PascalCase + Model | `ProductModel`, `UserModel` |
| View | kebab-case.php | `product-detail.php`, `order-detail.php` |
| Database table | snake_case | `product_variants`, `order_details` |
| URL | kebab-case | `/home/products`, `/admin/orders` |
| Function | camelCase | `formatMoney()`, `getProducts()` |

---

## 🛠 CÔNG NGHỆ SỬ DỤNG

- **Backend:** PHP 8+ (Native, không framework)
- **Database:** MySQL
- **Frontend:** HTML, Tailwind CSS (CDN), JavaScript
- **Icons:** Font Awesome
- **Pattern:** MVC (Model-View-Controller)
