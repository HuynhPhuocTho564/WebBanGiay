<?php
/**
 * Admin Order Controller
 * Quản lý đơn hàng
 */

class AdminOrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireAdmin();
    }

    /**
     * Danh sách đơn hàng
     */
    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $search = $_GET['q'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($status) {
            $where .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($search) {
            $where .= " AND (id LIKE ? OR fullname LIKE ? OR phone_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $orders = $this->db->fetchAll(
            "SELECT o.*, u.email as user_email 
             FROM orders o 
             LEFT JOIN tblUser u ON o.user_id = u.id 
             WHERE $where 
             ORDER BY o.order_date DESC",
            $params
        );

        $data = [
            'pageTitle' => 'Quản lý đơn hàng',
            'orders' => $orders,
            'currentStatus' => $status
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/orders/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Chi tiết đơn hàng
     */
    public function detail(int $id = 0): void
    {
        $order = $this->db->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
        
        if (!$order) {
            Session::flash('error', 'Đơn hàng không tồn tại');
            $this->redirect('adminorder');
        }

        $orderItems = $this->db->fetchAll(
            "SELECT od.*, pv.size, pv.color, p.name, p.thumbnail, p.slug
             FROM order_details od
             JOIN product_variants pv ON od.product_variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             WHERE od.order_id = ?",
            [$id]
        );

        $data = [
            'pageTitle' => 'Chi tiết đơn hàng #' . $id,
            'order' => $order,
            'orderItems' => $orderItems
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/orders/detail', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * BUG #10, #11 FIX: Validate trạng thái và hoàn kho khi hủy
     */
    public function updateStatus(): void
    {
        if (!$this->isPost()) {
            $this->redirect('adminorder');
        }

        $id = (int) $this->input('order_id');
        $newStatus = $this->input('status');

        // Lấy đơn hàng hiện tại
        $order = $this->db->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);

        if (!$order) {
            Session::flash('error', 'Đơn hàng không tồn tại');
            $this->redirect('adminorder');
            return;
        }

        $currentStatus = $order['status'];

        // BUG #10 FIX: Không cho phép thay đổi trạng thái đơn đã hủy hoặc hoàn thành
        if (in_array($currentStatus, ['cancelled', 'returned'])) {
            Session::flash('error', 'Không thể thay đổi trạng thái đơn hàng đã hủy/hoàn trả');
            $this->redirect('adminorder/detail/' . $id);
            return;
        }

        // Định nghĩa luồng trạng thái hợp lệ
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping' => ['completed', 'returned'],
            'completed' => [] // Không thể thay đổi
        ];

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            Session::flash('error', 'Không thể chuyển từ trạng thái "' . $currentStatus . '" sang "' . $newStatus . '"');
            $this->redirect('adminorder/detail/' . $id);
            return;
        }

        // Bắt đầu transaction
        $this->db->beginTransaction();

        try {
            // BUG #11 FIX: Hoàn kho khi hủy hoặc hoàn trả
            if (in_array($newStatus, ['cancelled', 'returned'])) {
                $orderItems = $this->db->fetchAll(
                    "SELECT product_variant_id, quantity FROM order_details WHERE order_id = ?",
                    [$id]
                );

                foreach ($orderItems as $item) {
                    // Chỉ hoàn kho nếu variant còn tồn tại
                    $this->db->query(
                        "UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?",
                        [$item['quantity'], $item['product_variant_id']]
                    );
                }

                // Hoàn lại usage_limit của coupon nếu có
                if ($order['coupon_id']) {
                    $this->db->query(
                        "UPDATE coupons SET usage_limit = usage_limit + 1 WHERE id = ?",
                        [$order['coupon_id']]
                    );
                }
            }

            // Cập nhật trạng thái
            $this->db->query("UPDATE orders SET status = ? WHERE id = ?", [$newStatus, $id]);

            $this->db->commit();
            Session::flash('success', 'Cập nhật trạng thái thành công');

        } catch (Exception $e) {
            $this->db->rollback();
            Session::flash('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }

        $this->redirect('adminorder/detail/' . $id);
    }
}
