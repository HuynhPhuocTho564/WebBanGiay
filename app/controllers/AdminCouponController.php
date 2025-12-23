<?php
/**
 * Admin Coupon Controller
 * Quản lý mã giảm giá
 */

class AdminCouponController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireSuperAdmin();
    }

    /**
     * Danh sách mã giảm giá
     */
    public function index(): void
    {
        $coupons = $this->db->fetchAll("SELECT * FROM coupons ORDER BY id DESC");

        $data = [
            'pageTitle' => 'Quản lý mã giảm giá',
            'coupons' => $coupons
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/coupons/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Thêm mã giảm giá
     * BUG #15, #17 FIX: Validate code trùng và ngày hợp lệ
     */
    public function store(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admincoupon');
        }

        $code = strtoupper(trim($this->input('code')));
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $discountValue = (float) $this->input('discount_value');

        // Validate code không rỗng
        if (empty($code)) {
            Session::flash('error', 'Vui lòng nhập mã giảm giá');
            $this->redirect('admincoupon');
            return;
        }

        // BUG #15 FIX: Kiểm tra code trùng
        $exists = $this->db->fetchOne("SELECT id FROM coupons WHERE code = ?", [$code]);
        if ($exists) {
            Session::flash('error', 'Mã giảm giá đã tồn tại');
            $this->redirect('admincoupon');
            return;
        }

        // BUG #17 FIX: Validate ngày
        if (empty($startDate) || empty($endDate)) {
            Session::flash('error', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            $this->redirect('admincoupon');
            return;
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            Session::flash('error', 'Ngày bắt đầu phải trước ngày kết thúc');
            $this->redirect('admincoupon');
            return;
        }

        // Validate giá trị giảm
        if ($discountValue <= 0) {
            Session::flash('error', 'Giá trị giảm phải lớn hơn 0');
            $this->redirect('admincoupon');
            return;
        }

        $this->db->insert('coupons', [
            'code' => $code,
            'discount_type' => $this->input('discount_type'),
            'discount_value' => $discountValue,
            'min_order_value' => $this->input('min_order_value') ?: 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'usage_limit' => $this->input('usage_limit') ?: 100,
            'status' => 1
        ]);

        Session::flash('success', 'Thêm mã giảm giá thành công');
        $this->redirect('admincoupon');
    }

    /**
     * Cập nhật mã giảm giá
     * BUG #15, #17 FIX: Validate code trùng và ngày hợp lệ
     */
    public function update(int $id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('admincoupon');
        }

        $code = strtoupper(trim($this->input('code')));
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $discountValue = (float) $this->input('discount_value');

        // Validate code không rỗng
        if (empty($code)) {
            Session::flash('error', 'Vui lòng nhập mã giảm giá');
            $this->redirect('admincoupon');
            return;
        }

        // BUG #15 FIX: Kiểm tra code trùng (loại trừ chính nó)
        $exists = $this->db->fetchOne("SELECT id FROM coupons WHERE code = ? AND id != ?", [$code, $id]);
        if ($exists) {
            Session::flash('error', 'Mã giảm giá đã tồn tại');
            $this->redirect('admincoupon');
            return;
        }

        // BUG #17 FIX: Validate ngày
        if (empty($startDate) || empty($endDate)) {
            Session::flash('error', 'Vui lòng chọn ngày bắt đầu và kết thúc');
            $this->redirect('admincoupon');
            return;
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            Session::flash('error', 'Ngày bắt đầu phải trước ngày kết thúc');
            $this->redirect('admincoupon');
            return;
        }

        // Validate giá trị giảm
        if ($discountValue <= 0) {
            Session::flash('error', 'Giá trị giảm phải lớn hơn 0');
            $this->redirect('admincoupon');
            return;
        }

        $this->db->update('coupons', [
            'code' => $code,
            'discount_type' => $this->input('discount_type'),
            'discount_value' => $discountValue,
            'min_order_value' => $this->input('min_order_value') ?: 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'usage_limit' => $this->input('usage_limit') ?: 100,
            'status' => $this->input('status') ? 1 : 0
        ], 'id = ?', [$id]);

        Session::flash('success', 'Cập nhật mã giảm giá thành công');
        $this->redirect('admincoupon');
    }

    /**
     * Xóa mã giảm giá
     */
    public function delete(int $id = 0): void
    {
        $this->db->query("DELETE FROM coupons WHERE id = ?", [$id]);
        Session::flash('success', 'Xóa mã giảm giá thành công');
        $this->redirect('admincoupon');
    }
}
