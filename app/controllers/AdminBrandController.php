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
        Middleware::requireAdmin();
    }

    /**
     * Danh sách thương hiệu
     */
    public function index(): void
    {
        $brands = $this->db->fetchAll(
            "SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id) as product_count 
             FROM brands b ORDER BY b.name"
        );

        $data = [
            'pageTitle' => 'Quản lý thương hiệu',
            'brands' => $brands
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/brands/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Thêm thương hiệu
     * BUG #16 FIX: Kiểm tra tên trùng
     */
    public function store(): void
    {
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

        Session::flash('success', 'Thêm thương hiệu thành công');
        $this->redirect('adminbrand');
    }

    /**
     * Cập nhật thương hiệu
     * BUG #16 FIX: Kiểm tra tên trùng
     */
    public function update(int $id = 0): void
    {
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

        Session::flash('success', 'Cập nhật thương hiệu thành công');
        $this->redirect('adminbrand');
    }

    /**
     * Xóa thương hiệu
     */
    public function delete(int $id = 0): void
    {
        $count = $this->db->count("SELECT COUNT(*) FROM products WHERE brand_id = ?", [$id]);
        
        if ($count > 0) {
            Session::flash('error', 'Không thể xóa thương hiệu đang có sản phẩm');
            $this->redirect('adminbrand');
        }

        $this->db->query("DELETE FROM brands WHERE id = ?", [$id]);
        Session::flash('success', 'Xóa thương hiệu thành công');
        $this->redirect('adminbrand');
    }
}
