<?php
/**
 * Admin Coupon Controller
 * Quản lý mã giảm giá
 */

class AdmincouponController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Chỉ Admin mới được truy cập quản lý mã giảm giá
        Middleware::requireSuperAdmin();
    }

    /**
     * Danh sách mã giảm giá
     */
    public function index(): void
    {
        $coupons = $this->db->fetchAll("SELECT * FROM coupons ORDER BY created_at DESC");

        $data = [
            'pageTitle' => 'Quản lý mã giảm giá',
            'coupons' => $coupons
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/coupons/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Form thêm mã giảm giá
     */
    public function create(): void
    {
        if ($this->isPost()) {
            $this->store();
            return;
        }

        $data = [
            'pageTitle' => 'Thêm mã giảm giá'
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/coupons/create', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Lưu mã giảm giá mới
     */
    private function store(): void
    {
        $code = strtoupper(trim($this->input('code')));
        $name = trim($this->input('name'));
        $description = trim($this->input('description'));
        $discountType = $this->input('discount_type');
        $discountValue = floatval($this->input('discount_value'));
        $maxDiscount = $this->input('max_discount') ? floatval($this->input('max_discount')) : null;
        $minOrderValue = floatval($this->input('min_order_value'));
        $startDate = $this->input('start_date') ?: null;
        $endDate = $this->input('end_date') ?: null;
        $usageLimit = intval($this->input('usage_limit')) ?: 100;
        $status = $this->input('status') ? 1 : 0;

        // Validate
        if (empty($code)) {
            Session::flash('error', 'Vui lòng nhập mã giảm giá');
            $this->redirect('admincoupon/create');
            return;
        }

        // Kiểm tra mã trùng
        $exists = $this->db->fetchOne("SELECT id FROM coupons WHERE code = ?", [$code]);
        if ($exists) {
            Session::flash('error', 'Mã giảm giá đã tồn tại');
            $this->redirect('admincoupon/create');
            return;
        }

        // Validate giá trị giảm
        if ($discountType === 'percent' && $discountValue > 100) {
            Session::flash('error', 'Phần trăm giảm không được vượt quá 100%');
            $this->redirect('admincoupon/create');
            return;
        }

        $this->db->insert('coupons', [
            'code' => $code,
            'name' => $name ?: $code,
            'description' => $description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'max_discount' => $maxDiscount,
            'min_order_value' => $minOrderValue,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'usage_limit' => $usageLimit,
            'used_count' => 0,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $couponId = $this->db->lastInsertId();
        
        // Ghi log tạo mã giảm giá
        logAction('create', "Tạo mã giảm giá: $code", 'coupon', (int)$couponId);

        Session::flash('success', 'Thêm mã giảm giá thành công');
        $this->redirect('admincoupon');
    }

    /**
     * Form sửa mã giảm giá
     */
    public function edit(int $id = 0): void
    {
        if ($this->isPost()) {
            $this->update($id);
            return;
        }

        $coupon = $this->db->fetchOne("SELECT * FROM coupons WHERE id = ?", [$id]);
        
        if (!$coupon) {
            Session::flash('error', 'Không tìm thấy mã giảm giá');
            $this->redirect('admincoupon');
            return;
        }

        $data = [
            'pageTitle' => 'Sửa mã giảm giá',
            'coupon' => $coupon
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/coupons/edit', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Cập nhật mã giảm giá
     */
    private function update(int $id): void
    {
        $code = strtoupper(trim($this->input('code')));
        $name = trim($this->input('name'));
        $description = trim($this->input('description'));
        $discountType = $this->input('discount_type');
        $discountValue = floatval($this->input('discount_value'));
        $maxDiscount = $this->input('max_discount') ? floatval($this->input('max_discount')) : null;
        $minOrderValue = floatval($this->input('min_order_value'));
        $startDate = $this->input('start_date') ?: null;
        $endDate = $this->input('end_date') ?: null;
        $usageLimit = intval($this->input('usage_limit')) ?: 100;
        $status = $this->input('status') ? 1 : 0;

        // Validate
        if (empty($code)) {
            Session::flash('error', 'Vui lòng nhập mã giảm giá');
            $this->redirect('admincoupon/edit/' . $id);
            return;
        }

        // Kiểm tra mã trùng (loại trừ chính nó)
        $exists = $this->db->fetchOne("SELECT id FROM coupons WHERE code = ? AND id != ?", [$code, $id]);
        if ($exists) {
            Session::flash('error', 'Mã giảm giá đã tồn tại');
            $this->redirect('admincoupon/edit/' . $id);
            return;
        }

        // Validate giá trị giảm
        if ($discountType === 'percent' && $discountValue > 100) {
            Session::flash('error', 'Phần trăm giảm không được vượt quá 100%');
            $this->redirect('admincoupon/edit/' . $id);
            return;
        }

        $this->db->update('coupons', [
            'code' => $code,
            'name' => $name ?: $code,
            'description' => $description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'max_discount' => $maxDiscount,
            'min_order_value' => $minOrderValue,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'usage_limit' => $usageLimit,
            'status' => $status
        ], 'id = ?', [$id]);

        // Ghi log cập nhật mã giảm giá
        logAction('update', "Cập nhật mã giảm giá: $code", 'coupon', $id);

        Session::flash('success', 'Cập nhật mã giảm giá thành công');
        $this->redirect('admincoupon');
    }

    /**
     * Bật/tắt mã giảm giá
     */
    public function toggle(int $id = 0): void
    {
        $coupon = $this->db->fetchOne("SELECT status FROM coupons WHERE id = ?", [$id]);
        
        if ($coupon) {
            $newStatus = $coupon['status'] ? 0 : 1;
            $this->db->update('coupons', ['status' => $newStatus], 'id = ?', [$id]);
            Session::flash('success', $newStatus ? 'Đã kích hoạt mã giảm giá' : 'Đã tắt mã giảm giá');
        }

        $this->redirect('admincoupon');
    }

    /**
     * Xóa mã giảm giá
     */
    public function delete(int $id = 0): void
    {
        // Kiểm tra xem mã đã được sử dụng chưa
        $coupon = $this->db->fetchOne("SELECT code, used_count FROM coupons WHERE id = ?", [$id]);
        
        if ($coupon && $coupon['used_count'] > 0) {
            Session::flash('error', 'Không thể xóa mã đã được sử dụng');
            $this->redirect('admincoupon');
            return;
        }

        $this->db->query("DELETE FROM coupons WHERE id = ?", [$id]);
        
        // Ghi log xóa mã giảm giá
        logAction('delete', "Xóa mã giảm giá: " . ($coupon['code'] ?? "ID $id"), 'coupon', $id);
        
        Session::flash('success', 'Xóa mã giảm giá thành công');
        $this->redirect('admincoupon');
    }
}
