<?php
/**
 * Admin Category Controller
 * Quản lý danh mục
 */

class AdminCategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAdmin();
    }

    /**
     * Danh sách danh mục
     */
    public function index(): void
    {
        $categories = $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
             FROM categories c ORDER BY c.name"
        );

        $data = [
            'pageTitle' => 'Quản lý danh mục',
            'categories' => $categories
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/categories/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Thêm danh mục
     * BUG #16 FIX: Kiểm tra tên trùng
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admincategory');
        }

        $name = trim($this->input('name'));

        if (empty($name)) {
            Session::flash('error', 'Vui lòng nhập tên danh mục');
            $this->redirect('admincategory');
            return;
        }

        // Kiểm tra tên trùng (không phân biệt hoa thường)
        $exists = $this->db->fetchOne("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)", [$name]);
        if ($exists) {
            Session::flash('error', 'Tên danh mục đã tồn tại');
            $this->redirect('admincategory');
            return;
        }

        $this->db->insert('categories', [
            'name' => $name,
            'slug' => createSlug($name),
            'status' => 1
        ]);

        Session::flash('success', 'Thêm danh mục thành công');
        $this->redirect('admincategory');
    }

    /**
     * Cập nhật danh mục
     * BUG #16 FIX: Kiểm tra tên trùng
     */
    public function update(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admincategory');
        }

        $name = trim($this->input('name'));

        if (empty($name)) {
            Session::flash('error', 'Vui lòng nhập tên danh mục');
            $this->redirect('admincategory');
            return;
        }

        // Kiểm tra tên trùng (loại trừ chính nó)
        $exists = $this->db->fetchOne("SELECT id FROM categories WHERE LOWER(name) = LOWER(?) AND id != ?", [$name, $id]);
        if ($exists) {
            Session::flash('error', 'Tên danh mục đã tồn tại');
            $this->redirect('admincategory');
            return;
        }

        $status = $this->input('status') ? 1 : 0;

        $this->db->update('categories', [
            'name' => $name,
            'slug' => createSlug($name),
            'status' => $status
        ], 'id = ?', [$id]);

        Session::flash('success', 'Cập nhật danh mục thành công');
        $this->redirect('admincategory');
    }

    /**
     * Xóa danh mục
     */
    public function delete(int $id = 0): void
    {
        // Kiểm tra có sản phẩm không
        $count = $this->db->count("SELECT COUNT(*) FROM products WHERE category_id = ?", [$id]);
        
        if ($count > 0) {
            Session::flash('error', 'Không thể xóa danh mục đang có sản phẩm');
            $this->redirect('admincategory');
        }

        $this->db->query("DELETE FROM categories WHERE id = ?", [$id]);
        Session::flash('success', 'Xóa danh mục thành công');
        $this->redirect('admincategory');
    }
}
