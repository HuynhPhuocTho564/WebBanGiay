# TÀI LIỆU CẤU TRÚC DỰ ÁN - SNEAKER STORE

## THƯ MỤC GỐC

- `index.php` - Entry point chính, load config và khởi tạo App
- `.htaccess` - Rewrite URL về index.php
- `README.md` - Hướng dẫn cài đặt và chạy project
- `LINKS.txt` - Danh sách các link truy cập

---

## app/core/ - Lõi hệ thống

- `App.php` - Router, parse URL và điều hướng đến Controller/Method tương ứng
- `Controller.php` - Base Controller, class cha cung cấp các method: view(), redirect(), json(), input(), isPost()
- `Database.php` - Database wrapper PDO, cung cấp: query(), fetchOne(), fetchAll(), insert(), update(), delete(), transaction
- `Session.php` - Quản lý session: login/logout, flash messages, CSRF token, role constants
- `Middleware.php` - Kiểm tra quyền truy cập: auth(), guest(), admin(), staff()

---

## app/controllers/ - Xử lý logic

- `HomeController.php` - Trang chủ, danh sách sản phẩm, chi tiết sản phẩm, tìm kiếm, đánh giá, các trang thông tin
- `AuthController.php` - Đăng nhập, đăng ký, đăng xuất, Google OAuth
- `CartController.php` - Giỏ hàng: thêm/sửa/xóa sản phẩm (AJAX)
- `CheckoutController.php` - Thanh toán: form checkout, áp dụng coupon, đặt hàng với transaction
- `ProfileController.php` - Trang cá nhân: thông tin, đổi mật khẩu, đơn hàng, wishlist, hủy đơn, trả hàng
- `AdminController.php` - Dashboard admin: thống kê tổng quan, biểu đồ doanh thu
- `AdminProductController.php` - CRUD sản phẩm, quản lý variants (màu/size/tồn kho), gallery
- `AdminCategoryController.php` - CRUD danh mục sản phẩm
- `AdminBrandController.php` - CRUD thương hiệu
- `AdminOrderController.php` - Quản lý đơn hàng: danh sách, chi tiết, cập nhật trạng thái, xử lý trả hàng
- `AdminUserController.php` - Quản lý người dùng: CRUD, phân quyền
- `AdminCouponController.php` - Quản lý mã giảm giá
- `AdminReportController.php` - Báo cáo thống kê doanh thu (chỉ Admin)

---

## app/models/ - Tương tác database

- `ProductModel.php` - Truy vấn sản phẩm: getProducts(), findBySlug(), getVariants(), getGallery(), getBestSellers(), getOnSale()
- `UserModel.php` - Truy vấn user: findByEmail(), findById(), create(), update(), getWishlist()

---

## app/helpers/

- `functions.php` - Helper functions: formatMoney(), productImage(), avatar(), discountPercent(), csrfField(), createSlug(), roleName()

---

## views/layouts/ - Layout client

- `header.php` - Header: logo, menu, search, cart icon, user dropdown
- `footer.php` - Footer: thông tin liên hệ, links, toast notification, loading overlay

---

## views/components/

- `product-card.php` - Card hiển thị sản phẩm: ảnh, tên, giá, badge giảm giá

---

## views/client/ - Trang khách hàng

- `home.php` - Trang chủ: banner, sản phẩm mới, bán chạy, khuyến mãi
- `products.php` - Danh sách sản phẩm: filter, sort, pagination
- `product-detail.php` - Chi tiết sản phẩm: gallery zoom, chọn màu/size, đánh giá

### views/client/auth/
- `login.php` - Form đăng nhập + Google login
- `register.php` - Form đăng ký

### views/client/cart/
- `index.php` - Giỏ hàng: danh sách, cập nhật số lượng, xóa, tổng tiền

### views/client/checkout/
- `index.php` - Trang thanh toán: form địa chỉ, phương thức, mã giảm giá

### views/client/profile/
- `_sidebar.php` - Sidebar menu trang cá nhân
- `index.php` - Thông tin cá nhân, đổi mật khẩu
- `orders.php` - Danh sách đơn hàng
- `order-detail.php` - Chi tiết đơn hàng, hủy đơn, trả hàng
- `wishlist.php` - Danh sách yêu thích
- `purchase-history.php` - Lịch sử mua hàng

### views/client/pages/
- `guide.php` - Hướng dẫn mua hàng
- `return-policy.php` - Chính sách đổi trả
- `warranty.php` - Chính sách bảo hành
- `size-guide.php` - Hướng dẫn chọn size

---

## views/admin/ - Trang quản trị

### views/admin/layouts/
- `header.php` - Header admin: sidebar menu, topbar, user dropdown
- `footer.php` - Footer admin: scripts, toast

### views/admin/dashboard/
- `index.php` - Dashboard: thống kê cards, biểu đồ doanh thu, đơn hàng gần đây

### views/admin/products/
- `index.php` - Danh sách sản phẩm
- `create.php` - Form thêm sản phẩm
- `edit.php` - Form sửa sản phẩm, variants, gallery

### views/admin/categories/
- `index.php` - CRUD danh mục (modal form)

### views/admin/brands/
- `index.php` - CRUD thương hiệu (modal form)

### views/admin/orders/
- `index.php` - Danh sách đơn hàng
- `detail.php` - Chi tiết đơn hàng, cập nhật trạng thái

### views/admin/users/
- `index.php` - Danh sách users
- `create.php` - Form thêm user
- `edit.php` - Form sửa user, phân quyền

### views/admin/coupons/
- `index.php` - CRUD mã giảm giá

### views/admin/reports/
- `index.php` - Báo cáo doanh thu (chỉ Admin)

---

## config/

- `config.php` - Cấu hình: DB connection, BASE_URL, SITE_NAME, ITEMS_PER_PAGE
- `google_config.php` - Cấu hình Google OAuth: Client ID, Secret, Redirect URI

---

## database/

- `schema.sql` - Script tạo toàn bộ database và sample data
- `fix_images.sql` - Script cập nhật ảnh sản phẩm từ Unsplash
- `create_reviews_table.sql` - Script tạo bảng reviews

---

## public/

- `index.php` - Entry point (được .htaccess redirect tới)
- `.htaccess` - Rewrite rules
- `assets/css/` - File CSS (Tailwind)
- `assets/js/` - File JavaScript
- `assets/images/` - Ảnh tĩnh (logo, banner)
- `assets/fonts/` - Font chữ
- `uploads/products/` - Ảnh sản phẩm upload
- `uploads/avatars/` - Avatar người dùng
- `uploads/returns/` - Ảnh yêu cầu trả hàng

---

## HỆ THỐNG PHÂN QUYỀN

- Role 0 (Khách hàng) - Chỉ truy cập trang client
- Role 1 (Nhân viên) - Admin panel (không xem báo cáo, không xóa sản phẩm)
- Role 2 (Admin) - Toàn quyền
