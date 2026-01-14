<?php
/**
 * Admin Brand Controller
 * Quản lý thương hiệu
 */

class AdminBrandController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Chỉ Admin mới được truy cập quản lý thương hiệu
        Middleware::requireSuperAdmin();
    }

    /**
     * Danh sách thương hiệu
     */
    public function index(): void
    {
        // Staff chỉ được xem, không được thêm/sửa/xóa
        $canEdit = Session::isAdmin();
        
        $brands = $this->db->fetchAll(
            "SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id) as product_count 
             FROM brands b ORDER BY b.name"
        );

        $data = [
            'pageTitle' => 'Quản lý thương hiệu',
            'brands' => $brands,
            'canEdit' => $canEdit
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/brands/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Thêm thương hiệu (Chỉ Admin)
     * BUG #16 FIX: Kiểm tra tên trùng
     */
    public function store(): void
    {
        Middleware::requireSuperAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('adminbrand');
        }

        $name = trim($this->input('name'));

        if (empty($name)) {
            Session::flash('error', 'Vui lòng nhập tên thương hiệu');
            $this->redirect('adminbrand');
            return;
        }

        // Kiểm tra tên trùng (không phân biệt hoa thường)
        $exists = $this->db->fetchOne("SELECT id FROM brands WHERE LOWER(name) = LOWER(?)", [$name]);
        if ($exists) {
            Session::flash('error', 'Tên thương hiệu đã tồn tại');
            $this->redirect('adminbrand');
            return;
        }

        $this->db->insert('brands', [
            'name' => $name,
            'slug' => createSlug($name),
            'logo' => ''
        ]);

        $brandId = $this->db->lastInsertId();
        logAction('create', "Tạo thương hiệu: $name", 'brand', (int)$brandId);

        Session::flash('success', 'Thêm thương hiệu thành công');
        $this->redirect('adminbrand');
    }

    /**
     * Cập nhật thương hiệu (Chỉ Admin)
     * BUG #16 FIX: Kiểm tra tên trùng
     */
    public function update(int $id = 0): void
    {
        Middleware::requireSuperAdmin();
        
        if (!$this->isPost()) {
            $this->redirect('adminbrand');
        }

        $name = trim($this->input('name'));

        if (empty($name)) {
            Session::flash('error', 'Vui lòng nhập tên thương hiệu');
            $this->redirect('adminbrand');
            return;
        }

        // Kiểm tra tên trùng (loại trừ chính nó)
        $exists = $this->db->fetchOne("SELECT id FROM brands WHERE LOWER(name) = LOWER(?) AND id != ?", [$name, $id]);
        if ($exists) {
            Session::flash('error', 'Tên thương hiệu đã tồn tại');
            $this->redirect('adminbrand');
            return;
        }

        $status = $this->input('status') ? 1 : 0;

        $this->db->update('brands', [
            'name' => $name,
            'slug' => createSlug($name),
            'status' => $status
        ], 'id = ?', [$id]);

        logAction('update', "Cập nhật thương hiệu: $name", 'brand', $id);

        Session::flash('success', 'Cập nhật thương hiệu thành công');
        $this->redirect('adminbrand');
    }

    /**
     * Xóa thương hiệu (Chỉ Admin)
     */
    public function delete(int $id = 0): void
    {
        Middleware::requireSuperAdmin();
        $count = $this->db->count("SELECT COUNT(*) FROM products WHERE brand_id = ?", [$id]);
        
        if ($count > 0) {
            Session::flash('error', 'Không thể xóa thương hiệu đang có sản phẩm');
            $this->redirect('adminbrand');
        }

        $brand = $this->db->fetchOne("SELECT name FROM brands WHERE id = ?", [$id]);
        $this->db->query("DELETE FROM brands WHERE id = ?", [$id]);
        
        logAction('delete', "Xóa thương hiệu: " . ($brand['name'] ?? "ID $id"), 'brand', $id);
        
        Session::flash('success', 'Xóa thương hiệu thành công');
        $this->redirect('adminbrand');
    }
}
