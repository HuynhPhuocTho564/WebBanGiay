<?php
/**
 * Admin Product Controller
 * Quản lý sản phẩm
 */

class AdminProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAdmin();
    }

    /**
     * Danh sách sản phẩm
     */
    public function index(): void
    {
        $search = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $brand = $_GET['brand'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($search) {
            $where .= " AND (p.name LIKE ? OR p.slug LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($category) {
            $where .= " AND p.category_id = ?";
            $params[] = $category;
        }
        
        if ($brand) {
            $where .= " AND p.brand_id = ?";
            $params[] = $brand;
        }
        
        $products = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, b.name as brand_name,
                    (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id) as total_stock
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE $where 
             ORDER BY p.created_at DESC",
            $params
        );

        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $brands = $this->db->fetchAll("SELECT * FROM brands ORDER BY name");

        $data = [
            'pageTitle' => 'Quản lý sản phẩm',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/products/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Form thêm sản phẩm
     */
    public function create(): void
    {
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $brands = $this->db->fetchAll("SELECT * FROM brands ORDER BY name");

        $data = [
            'pageTitle' => 'Thêm sản phẩm mới',
            'categories' => $categories,
            'brands' => $brands
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/products/create', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Lưu sản phẩm mới
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('adminproduct');
        }

        $name = trim($this->input('name'));

        // Kiểm tra trùng tên (không phân biệt hoa thường)
        $exists = $this->db->fetchOne(
            "SELECT id FROM products WHERE LOWER(name) = LOWER(?)",
            [$name]
        );

        if ($exists) {
            Session::flash('error', 'Tên sản phẩm đã tồn tại. Vui lòng chọn tên khác.');
            $this->redirect('adminproduct/create');
            return;
        }

        // BUG #6, #7 FIX: Validate giá trị
        $price = (float) $this->input('price');
        $discountPrice = (float) ($this->input('discount_price') ?: 0);

        if ($price <= 0) {
            Session::flash('error', 'Giá sản phẩm phải lớn hơn 0');
            $this->redirect('adminproduct/create');
            return;
        }

        if ($discountPrice < 0) {
            Session::flash('error', 'Giá giảm không được âm');
            $this->redirect('adminproduct/create');
            return;
        }

        if ($discountPrice > 0 && $discountPrice >= $price) {
            Session::flash('error', 'Giá giảm phải nhỏ hơn giá gốc');
            $this->redirect('adminproduct/create');
            return;
        }

        $data = [
            'name' => $name,
            'slug' => createSlug($name),
            'category_id' => $this->input('category_id') ?: null,
            'brand_id' => $this->input('brand_id') ?: null,
            'price' => $price,
            'discount_price' => $discountPrice,
            'gender' => $this->input('gender'),
            'description' => $this->input('description'),
            'thumbnail' => ''
        ];

        // Upload thumbnail
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $data['thumbnail'] = $this->uploadImage($_FILES['thumbnail']);
        }

        $productId = $this->db->insert('products', $data);

        // Thêm variants
        $sizes = $_POST['sizes'] ?? [];
        $colors = $_POST['colors'] ?? [];
        $stocks = $_POST['stocks'] ?? [];

        for ($i = 0; $i < count($sizes); $i++) {
            if (!empty($sizes[$i]) && !empty($colors[$i])) {
                $stockQty = isset($stocks[$i]) && $stocks[$i] !== '' ? (int)$stocks[$i] : 0;
                $this->db->insert('product_variants', [
                    'product_id' => $productId,
                    'size' => $sizes[$i],
                    'color' => $colors[$i],
                    'stock_quantity' => $stockQty
                ]);
            }
        }

        Session::flash('success', 'Thêm sản phẩm thành công');
        $this->redirect('adminproduct');
    }

    /**
     * Form sửa sản phẩm
     */
    public function edit(int $id = 0): void
    {
        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
        
        if (!$product) {
            Session::flash('error', 'Sản phẩm không tồn tại');
            $this->redirect('adminproduct');
        }

        $variants = $this->db->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ?",
            [$id]
        );

        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $brands = $this->db->fetchAll("SELECT * FROM brands ORDER BY name");

        $data = [
            'pageTitle' => 'Sửa sản phẩm',
            'product' => $product,
            'variants' => $variants,
            'categories' => $categories,
            'brands' => $brands
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/products/edit', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('adminproduct');
        }

        $name = trim($this->input('name'));

        // Kiểm tra trùng tên (không phân biệt hoa thường, loại trừ sản phẩm hiện tại)
        $exists = $this->db->fetchOne(
            "SELECT id FROM products WHERE LOWER(name) = LOWER(?) AND id != ?",
            [$name, $id]
        );

        if ($exists) {
            Session::flash('error', 'Tên sản phẩm đã tồn tại. Vui lòng chọn tên khác.');
            $this->redirect('adminproduct/edit/' . $id);
            return;
        }

        // BUG #6, #7 FIX: Validate giá trị
        $price = (float) $this->input('price');
        $discountPrice = (float) ($this->input('discount_price') ?: 0);

        if ($price <= 0) {
            Session::flash('error', 'Giá sản phẩm phải lớn hơn 0');
            $this->redirect('adminproduct/edit/' . $id);
            return;
        }

        if ($discountPrice < 0) {
            Session::flash('error', 'Giá giảm không được âm');
            $this->redirect('adminproduct/edit/' . $id);
            return;
        }

        if ($discountPrice > 0 && $discountPrice >= $price) {
            Session::flash('error', 'Giá giảm phải nhỏ hơn giá gốc');
            $this->redirect('adminproduct/edit/' . $id);
            return;
        }

        $data = [
            'name' => $name,
            'slug' => createSlug($name),
            'category_id' => $this->input('category_id') ?: null,
            'brand_id' => $this->input('brand_id') ?: null,
            'price' => $price,
            'discount_price' => $discountPrice,
            'gender' => $this->input('gender'),
            'description' => $this->input('description')
        ];

        // Upload thumbnail mới nếu có
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $data['thumbnail'] = $this->uploadImage($_FILES['thumbnail']);
        }

        $this->db->update('products', $data, 'id = ?', [$id]);

        Session::flash('success', 'Cập nhật sản phẩm thành công');
        $this->redirect('adminproduct/edit/' . $id);
    }

    /**
     * Xóa sản phẩm (Chỉ Admin)
     */
    public function delete(int $id = 0): void
    {
        // BUG #4 FIX: Chỉ Admin mới được xóa sản phẩm
        Middleware::requireSuperAdmin();

        // Kiểm tra sản phẩm có trong đơn hàng chưa hoàn thành không
        $inOrders = $this->db->count(
            "SELECT COUNT(*) FROM order_details od 
             JOIN product_variants pv ON od.product_variant_id = pv.id 
             JOIN orders o ON od.order_id = o.id
             WHERE pv.product_id = ? AND o.status NOT IN ('completed', 'cancelled', 'returned')",
            [$id]
        );

        if ($inOrders > 0) {
            Session::flash('error', 'Không thể xóa sản phẩm đang có trong đơn hàng chưa hoàn thành');
            $this->redirect('adminproduct');
            return;
        }

        // Xóa variants trước, sau đó xóa product
        $this->db->query("DELETE FROM product_variants WHERE product_id = ?", [$id]);
        $this->db->query("DELETE FROM wishlists WHERE product_id = ?", [$id]);
        $this->db->query("DELETE FROM products WHERE id = ?", [$id]);
        
        Session::flash('success', 'Xóa sản phẩm thành công');
        $this->redirect('adminproduct');
    }

    /**
     * Upload ảnh
     */
    private function uploadImage(array $file): string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return '';
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;

        if (!is_dir(PRODUCT_PATH)) {
            mkdir(PRODUCT_PATH, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], PRODUCT_PATH . '/' . $filename)) {
            return $filename;
        }
        return '';
    }
}
