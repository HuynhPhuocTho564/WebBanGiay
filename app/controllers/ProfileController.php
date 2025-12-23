<?php
/**
 * Profile Controller
 * Xử lý trang cá nhân, đơn hàng, yêu thích
 */

class ProfileController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        Middleware::requireLogin();
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Trang tài khoản
     */
    public function index(): void
    {
        $user = $this->userModel->findById(Session::userId());

        $data = [
            'pageTitle' => 'Tài khoản - ' . SITE_NAME,
            'user' => $user
        ];

        $this->view('layouts/header', $data);
        $this->view('client/profile/index', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Cập nhật thông tin
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->redirect('profile');
        }

        if (!Middleware::verifyCsrf()) {
            Session::flash('error', 'Phiên làm việc hết hạn');
            $this->redirect('profile');
        }

        $data = [
            'fullname' => $this->input('fullname'),
            'phone_number' => $this->input('phone_number'),
            'dob' => $this->input('dob') ?: null,
            'address' => $this->input('address'),
        ];

        // Upload avatar mới nếu có
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarName = $this->uploadAvatar($_FILES['avatar']);
            if ($avatarName) {
                $data['avatar'] = $avatarName;
            }
        }

        $this->userModel->update(Session::userId(), $data);

        // Cập nhật session
        $user = $this->userModel->findById(Session::userId());
        Session::login($user);

        Session::flash('success', 'Cập nhật thông tin thành công');
        $this->redirect('profile');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(): void
    {
        if (!$this->isPost()) {
            $this->redirect('profile');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->findById(Session::userId());

        // Validate
        if (!password_verify($currentPassword, $user['password'])) {
            Session::flash('error', 'Mật khẩu hiện tại không đúng');
            $this->redirect('profile');
        }

        if (strlen($newPassword) < 6) {
            Session::flash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự');
            $this->redirect('profile');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'Mật khẩu xác nhận không khớp');
            $this->redirect('profile');
        }

        $this->userModel->changePassword(Session::userId(), $newPassword);

        Session::flash('success', 'Đổi mật khẩu thành công');
        $this->redirect('profile');
    }

    /**
     * Trang đơn hàng (đang xử lý)
     */
    public function orders(): void
    {
        $orders = $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? AND status NOT IN ('completed', 'cancelled', 'returned') ORDER BY order_date DESC",
            [Session::userId()]
        );

        // Lấy sản phẩm đầu tiên của mỗi đơn hàng để hiển thị preview
        foreach ($orders as &$order) {
            $order['items'] = $this->db->fetchAll(
                "SELECT od.quantity, p.name, p.thumbnail, pv.size, pv.color
                 FROM order_details od
                 JOIN product_variants pv ON od.product_variant_id = pv.id
                 JOIN products p ON pv.product_id = p.id
                 WHERE od.order_id = ?
                 LIMIT 3",
                [$order['id']]
            );
            $order['item_count'] = $this->db->count(
                "SELECT COUNT(*) FROM order_details WHERE order_id = ?",
                [$order['id']]
            );
        }

        $data = [
            'pageTitle' => 'Đơn hàng của tôi - ' . SITE_NAME,
            'orders' => $orders
        ];

        $this->view('layouts/header', $data);
        $this->view('client/profile/orders', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Lịch sử mua hàng (đã hoàn thành/hủy)
     */
    public function purchaseHistory(): void
    {
        $orders = $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? AND status IN ('completed', 'cancelled', 'returned') ORDER BY order_date DESC",
            [Session::userId()]
        );

        foreach ($orders as &$order) {
            $order['items'] = $this->db->fetchAll(
                "SELECT od.quantity, p.name, p.thumbnail, pv.size, pv.color
                 FROM order_details od
                 JOIN product_variants pv ON od.product_variant_id = pv.id
                 JOIN products p ON pv.product_id = p.id
                 WHERE od.order_id = ?
                 LIMIT 3",
                [$order['id']]
            );
            $order['item_count'] = $this->db->count(
                "SELECT COUNT(*) FROM order_details WHERE order_id = ?",
                [$order['id']]
            );
        }

        $data = [
            'pageTitle' => 'Lịch sử mua hàng - ' . SITE_NAME,
            'orders' => $orders
        ];

        $this->view('layouts/header', $data);
        $this->view('client/profile/purchase-history', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Chi tiết đơn hàng
     */
    public function orderDetail(int $id = 0): void
    {
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$id, Session::userId()]
        );

        if (!$order) {
            Session::flash('error', 'Đơn hàng không tồn tại');
            $this->redirect('profile/orders');
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
            'pageTitle' => 'Chi tiết đơn hàng #' . $id . ' - ' . SITE_NAME,
            'order' => $order,
            'orderItems' => $orderItems
        ];

        $this->view('layouts/header', $data);
        $this->view('client/profile/order-detail', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Hủy đơn hàng (chỉ cho đơn pending)
     * BUG #9 FIX: Kiểm tra variant còn tồn tại trước khi hoàn kho
     */
    public function cancelOrder(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $orderId = (int) $this->input('order_id');

        // Kiểm tra đơn hàng thuộc về user và đang ở trạng thái pending
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'pending'",
            [$orderId, Session::userId()]
        );

        if (!$order) {
            $this->json(['success' => false, 'message' => 'Không thể hủy đơn hàng này']);
            return;
        }

        // Lấy lý do hủy
        $cancelReason = $this->input('cancel_reason') ?: 'Không có lý do';

        // Bắt đầu transaction
        $this->db->beginTransaction();

        try {
            // Cập nhật trạng thái thành cancelled và lưu lý do
            $this->db->query(
                "UPDATE orders SET status = 'cancelled', cancel_reason = ? WHERE id = ?",
                [$cancelReason, $orderId]
            );

            // Hoàn lại số lượng tồn kho
            $orderItems = $this->db->fetchAll(
                "SELECT product_variant_id, quantity FROM order_details WHERE order_id = ?",
                [$orderId]
            );

            foreach ($orderItems as $item) {
                // BUG #9 FIX: Chỉ hoàn kho nếu variant còn tồn tại
                $variantExists = $this->db->fetchOne(
                    "SELECT id FROM product_variants WHERE id = ?",
                    [$item['product_variant_id']]
                );

                if ($variantExists) {
                    $this->db->query(
                        "UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?",
                        [$item['quantity'], $item['product_variant_id']]
                    );
                }
            }

            // Hoàn lại usage_limit của coupon nếu có
            if ($order['coupon_id']) {
                $this->db->query(
                    "UPDATE coupons SET usage_limit = usage_limit + 1 WHERE id = ?",
                    [$order['coupon_id']]
                );
            }

            $this->db->commit();
            $this->json(['success' => true, 'message' => 'Đã hủy đơn hàng thành công']);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->json(['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại']);
        }
    }

    /**
     * Trang yêu thích
     */
    public function wishlist(): void
    {
        $wishlist = $this->db->fetchAll(
            "SELECT p.*, b.name as brand_name, w.created_at as added_at
             FROM wishlists w
             JOIN products p ON w.product_id = p.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE w.user_id = ?
             ORDER BY w.created_at DESC",
            [Session::userId()]
        );

        $data = [
            'pageTitle' => 'Sản phẩm yêu thích - ' . SITE_NAME,
            'wishlist' => $wishlist
        ];

        $this->view('layouts/header', $data);
        $this->view('client/profile/wishlist', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Thêm/Xóa yêu thích (AJAX)
     */
    public function toggleWishlist(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false], 400);
        }

        $productId = (int) $this->input('product_id');

        // Kiểm tra đã có trong wishlist chưa
        $exists = $this->db->fetchOne(
            "SELECT * FROM wishlists WHERE user_id = ? AND product_id = ?",
            [Session::userId(), $productId]
        );

        if ($exists) {
            $this->db->query(
                "DELETE FROM wishlists WHERE user_id = ? AND product_id = ?",
                [Session::userId(), $productId]
            );
            $this->json(['success' => true, 'action' => 'removed', 'message' => 'Đã xóa khỏi yêu thích']);
        } else {
            $this->db->query(
                "INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)",
                [Session::userId(), $productId]
            );
            $this->json(['success' => true, 'action' => 'added', 'message' => 'Đã thêm vào yêu thích']);
        }
    }

    /**
     * Upload avatar
     */
    private function uploadAvatar(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . Session::userId() . '_' . time() . '.' . $extension;

        if (!is_dir(AVATAR_PATH)) {
            mkdir(AVATAR_PATH, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], AVATAR_PATH . '/' . $filename)) {
            return $filename;
        }
        return null;
    }
}
