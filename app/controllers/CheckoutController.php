<?php
/**
 * Checkout Controller
 * Xử lý thanh toán
 */

class CheckoutController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireLogin();
        
        // Không cho Admin/Staff checkout
        if (Session::canAccessAdmin()) {
            Session::flash('error', 'Tài khoản quản trị không thể mua hàng. Vui lòng sử dụng tài khoản khách hàng.');
            $this->redirect('');
            exit;
        }
    }

    /**
     * Lấy giỏ hàng từ database
     */
    private function getCartFromDatabase(): array
    {
        $userId = Session::userId();
        
        return $this->db->fetchAll(
            "SELECT c.product_variant_id as id, c.product_variant_id as variant_id, c.quantity,
                    pv.size, pv.color, pv.stock_quantity,
                    p.id as product_id, p.name, p.slug, p.thumbnail, p.price, p.discount_price
             FROM cart c
             JOIN product_variants pv ON c.product_variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             WHERE c.user_id = ?",
            [$userId]
        );
    }

    /**
     * Trang thanh toán
     */
    public function index(): void
    {
        // Lấy giỏ hàng từ database
        $cartData = $this->getCartFromDatabase();
        
        if (empty($cartData)) {
            Session::flash('error', 'Giỏ hàng trống');
            $this->redirect('cart');
        }

        // Lấy danh sách sản phẩm đã chọn từ session (bắt buộc phải có)
        $selectedItems = Session::get('selected_cart_items', []);
        
        // Nếu không có sản phẩm được chọn, quay lại giỏ hàng
        if (empty($selectedItems)) {
            Session::flash('error', 'Vui lòng chọn sản phẩm để thanh toán');
            $this->redirect('cart');
        }

        // Lấy thông tin sản phẩm trong giỏ
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cartData as $item) {
            $variantId = $item['id']; // Dùng id thay vì variant_id
            
            // Chỉ lấy những sản phẩm đã chọn (so sánh cả string và int)
            if (!in_array($variantId, $selectedItems) && !in_array((string)$variantId, $selectedItems)) {
                continue;
            }

            $finalPrice = ($item['discount_price'] > 0 && $item['discount_price'] < $item['price']) 
                ? $item['discount_price'] 
                : $item['price'];
            $item['final_price'] = $finalPrice;
            $item['subtotal'] = $finalPrice * $item['quantity'];
            $cartItems[] = $item;
            $totalAmount += $item['subtotal'];
        }

        if (empty($cartItems)) {
            Session::flash('error', 'Vui lòng chọn sản phẩm để thanh toán');
            $this->redirect('cart');
        }

        // Lấy thông tin user
        $user = $this->db->fetchOne("SELECT * FROM tblUser WHERE id = ?", [Session::userId()]);

        $data = [
            'pageTitle' => 'Thanh toán - ' . SITE_NAME,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'user' => $user
        ];

        $this->view('layouts/header', $data);
        $this->view('client/checkout/index', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Lấy danh sách coupon khả dụng (AJAX)
     */
    public function getAvailableCoupons(): void
    {
        $total = (float) ($this->input('total') ?? 0);
        
        $coupons = $this->db->fetchAll(
            "SELECT * FROM coupons 
             WHERE status = 1 
               AND (start_date IS NULL OR start_date <= NOW())
               AND (end_date IS NULL OR end_date >= NOW())
               AND used_count < usage_limit
             ORDER BY discount_value DESC"
        );
        
        $this->json(['coupons' => $coupons]);
    }

    /**
     * Validate mã giảm giá (AJAX)
     */
    public function validateCoupon(): void
    {
        $code = strtoupper(trim($this->input('code') ?? ''));
        $total = (float) ($this->input('total') ?? 0);
        
        if (empty($code)) {
            $this->json(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá']);
            return;
        }
        
        $coupon = $this->db->fetchOne(
            "SELECT * FROM coupons WHERE code = ? AND status = 1",
            [$code]
        );
        
        if (!$coupon) {
            $this->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại']);
            return;
        }
        
        // Check thời hạn
        if ($coupon['start_date'] && strtotime($coupon['start_date']) > time()) {
            $this->json(['success' => false, 'message' => 'Mã giảm giá chưa có hiệu lực']);
            return;
        }
        
        if ($coupon['end_date'] && strtotime($coupon['end_date']) < time()) {
            $this->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn']);
            return;
        }
        
        // Check lượt dùng
        if ($coupon['used_count'] >= $coupon['usage_limit']) {
            $this->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng']);
            return;
        }
        
        // Check đơn tối thiểu
        if ($total < $coupon['min_order_value']) {
            $this->json([
                'success' => false, 
                'message' => 'Đơn hàng tối thiểu ' . number_format($coupon['min_order_value'], 0, ',', '.') . 'đ'
            ]);
            return;
        }
        
        $this->json(['success' => true, 'coupon' => $coupon]);
    }

    /**
     * Lưu danh sách sản phẩm đã chọn (AJAX)
     */
    public function setSelectedItems(): void
    {
        // Lấy raw data vì input() dùng htmlspecialchars làm hỏng JSON
        $items = $_POST['items'] ?? null;
        
        if ($items) {
            $selectedItems = json_decode($items, true);
            if (is_array($selectedItems) && !empty($selectedItems)) {
                // Đảm bảo là mảng string để so sánh đúng
                $selectedItems = array_map('strval', $selectedItems);
                Session::set('selected_cart_items', $selectedItems);
                $this->json(['success' => true, 'items' => $selectedItems]);
                return;
            }
        }
        
        $this->json(['success' => false, 'message' => 'No items selected']);
    }

    /**
     * Đặt hàng
     */
    public function placeOrder(): void
    {
        if (!$this->isPost()) {
            $this->redirect('checkout');
        }

        // Lấy giỏ hàng từ database
        $cartData = $this->getCartFromDatabase();
        if (empty($cartData)) {
            $this->redirect('cart');
        }

        // Lấy danh sách sản phẩm đã chọn
        $selectedItems = Session::get('selected_cart_items', []);
        
        if (empty($selectedItems)) {
            Session::flash('error', 'Vui lòng chọn sản phẩm để thanh toán');
            $this->redirect('cart');
        }

        // Validate thông tin
        $fullname = $this->input('fullname');
        $email = $this->input('email');
        $phone = $this->input('phone_number');
        $address = $this->input('address');
        $note = $this->input('note');
        $paymentMethod = $this->input('payment_method') ?: 'COD';
        $couponId = $this->input('coupon_id') ? (int) $this->input('coupon_id') : null;

        if (empty($fullname) || empty($phone) || empty($address)) {
            Session::flash('error', 'Vui lòng điền đầy đủ thông tin');
            $this->redirect('checkout');
        }

        // Validate số điện thoại
        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            Session::flash('error', 'Số điện thoại không hợp lệ (10-11 số)');
            $this->redirect('checkout');
        }

        // Validate email nếu có
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Email không hợp lệ');
            $this->redirect('checkout');
        }

        // Bắt đầu transaction
        $this->db->beginTransaction();

        try {
            // Tính tổng tiền và kiểm tra tồn kho
            $totalAmount = 0;
            $orderItems = [];
            $processedVariantIds = [];
            $userId = Session::userId();

            foreach ($cartData as $item) {
                $variantId = $item['id']; // Dùng id
                $quantity = $item['quantity'];
                
                // So sánh linh hoạt
                if (!in_array($variantId, $selectedItems) && !in_array((string)$variantId, $selectedItems)) {
                    continue;
                }

                // Lock row để kiểm tra tồn kho
                $variant = $this->db->fetchOne(
                    "SELECT pv.*, p.price, p.discount_price, p.name
                     FROM product_variants pv
                     JOIN products p ON pv.product_id = p.id
                     WHERE pv.id = ?
                     FOR UPDATE",
                    [$variantId]
                );

                if (!$variant) {
                    throw new Exception("Sản phẩm không còn tồn tại. Vui lòng kiểm tra lại giỏ hàng.");
                }

                if ($variant['stock_quantity'] < $quantity) {
                    throw new Exception("Sản phẩm '{$variant['name']}' (Size: {$variant['size']}, Màu: {$variant['color']}) chỉ còn {$variant['stock_quantity']} sản phẩm.");
                }

                $finalPrice = ($variant['discount_price'] > 0 && $variant['discount_price'] < $variant['price']) 
                    ? $variant['discount_price'] 
                    : $variant['price'];
                $subtotal = $finalPrice * $quantity;
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'variant_id' => $variantId,
                    'price' => $finalPrice,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                $processedVariantIds[] = $variantId;
            }

            if (empty($orderItems)) {
                throw new Exception("Không có sản phẩm hợp lệ để đặt hàng.");
            }

            // Xử lý coupon
            $discountAmount = 0;
            if ($couponId) {
                $coupon = $this->db->fetchOne(
                    "SELECT * FROM coupons WHERE id = ? AND status = 1 FOR UPDATE",
                    [$couponId]
                );
                
                if ($coupon) {
                    // Validate lại coupon
                    $isValid = true;
                    if ($coupon['start_date'] && strtotime($coupon['start_date']) > time()) $isValid = false;
                    if ($coupon['end_date'] && strtotime($coupon['end_date']) < time()) $isValid = false;
                    if ($coupon['used_count'] >= $coupon['usage_limit']) $isValid = false;
                    if ($totalAmount < $coupon['min_order_value']) $isValid = false;
                    
                    if ($isValid) {
                        // Tính giảm giá
                        if ($coupon['discount_type'] === 'percent') {
                            $discountAmount = $totalAmount * $coupon['discount_value'] / 100;
                            if ($coupon['max_discount'] && $discountAmount > $coupon['max_discount']) {
                                $discountAmount = $coupon['max_discount'];
                            }
                        } else {
                            $discountAmount = $coupon['discount_value'];
                        }
                        
                        // Tăng used_count
                        $this->db->query(
                            "UPDATE coupons SET used_count = used_count + 1 WHERE id = ?",
                            [$couponId]
                        );
                    } else {
                        $couponId = null; // Không áp dụng coupon không hợp lệ
                    }
                } else {
                    $couponId = null;
                }
            }

            $finalTotal = $totalAmount - $discountAmount;

            // Tạo đơn hàng
            $orderId = $this->db->insert('orders', [
                'user_id' => $userId,
                'fullname' => $fullname,
                'email' => $email,
                'phone_number' => $phone,
                'address' => $address,
                'note' => $note,
                'total_money' => $finalTotal,
                'payment_method' => $paymentMethod,
                'coupon_id' => $couponId,
                'status' => 'pending'
            ]);

            // Thêm chi tiết đơn hàng và trừ tồn kho
            foreach ($orderItems as $item) {
                $this->db->insert('order_details', [
                    'order_id' => $orderId,
                    'product_variant_id' => $item['variant_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_item_price' => $item['subtotal']
                ]);

                $affected = $this->db->rowCount(
                    "UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?",
                    [$item['quantity'], $item['variant_id'], $item['quantity']]
                );

                if ($affected === 0) {
                    throw new Exception("Không đủ tồn kho cho sản phẩm. Vui lòng thử lại.");
                }
            }

            // Xóa sản phẩm đã thanh toán khỏi giỏ hàng trong database
            foreach ($processedVariantIds as $variantId) {
                $this->db->query(
                    "DELETE FROM cart WHERE user_id = ? AND product_variant_id = ?",
                    [$userId, $variantId]
                );
            }

            // Commit transaction
            $this->db->commit();
            
            Session::remove('selected_cart_items');

            Session::flash('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $orderId);
            $this->redirect('checkout/success/' . $orderId);

        } catch (Exception $e) {
            $this->db->rollback();
            Session::flash('error', $e->getMessage());
            $this->redirect('checkout');
        }
    }

    /**
     * Trang đặt hàng thành công
     */
    public function success(int $orderId = 0): void
    {
        $order = $this->db->fetchOne(
            "SELECT o.*, c.code as coupon_code, c.name as coupon_name 
             FROM orders o 
             LEFT JOIN coupons c ON o.coupon_id = c.id
             WHERE o.id = ? AND o.user_id = ?",
            [$orderId, Session::userId()]
        );

        if (!$order) {
            $this->redirect('');
        }

        $data = [
            'pageTitle' => 'Đặt hàng thành công - ' . SITE_NAME,
            'order' => $order
        ];

        $this->view('layouts/header', $data);
        $this->view('client/checkout/success', $data);
        $this->view('layouts/footer', $data);
    }
}
