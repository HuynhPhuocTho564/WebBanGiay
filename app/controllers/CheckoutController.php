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
    }

    /**
     * Trang thanh toán
     */
    public function index(): void
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            Session::flash('error', 'Giỏ hàng trống');
            $this->redirect('cart');
        }

        // Lấy danh sách sản phẩm đã chọn từ session (nếu có)
        $selectedItems = Session::get('selected_cart_items', []);

        // Lấy thông tin sản phẩm trong giỏ
        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $variantId => $quantity) {
            // Nếu có danh sách đã chọn, chỉ lấy những sản phẩm đã chọn
            if (!empty($selectedItems) && !in_array($variantId, $selectedItems)) {
                continue;
            }

            $item = $this->db->fetchOne(
                "SELECT pv.*, p.name, p.slug, p.thumbnail, p.price, p.discount_price
                 FROM product_variants pv
                 JOIN products p ON pv.product_id = p.id
                 WHERE pv.id = ?",
                [$variantId]
            );

            if ($item) {
                $finalPrice = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
                $item['quantity'] = $quantity;
                $item['final_price'] = $finalPrice;
                $item['subtotal'] = $finalPrice * $quantity;
                $cartItems[] = $item;
                $totalAmount += $item['subtotal'];
            }
        }

        if (empty($cartItems)) {
            Session::flash('error', 'Vui lòng chọn sản phẩm để thanh toán');
            $this->redirect('cart');
        }

        // Lấy mã giảm giá đang hoạt động
        $coupons = $this->db->fetchAll(
            "SELECT * FROM coupons WHERE status = 1 AND start_date <= NOW() AND end_date >= NOW() AND usage_limit > 0"
        );

        // Lấy thông tin user
        $user = $this->db->fetchOne("SELECT * FROM tblUser WHERE id = ?", [Session::userId()]);

        $data = [
            'pageTitle' => 'Thanh toán - ' . SITE_NAME,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'coupons' => $coupons,
            'user' => $user
        ];

        $this->view('layouts/header', $data);
        $this->view('client/checkout/index', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Áp dụng mã giảm giá (AJAX)
     */
    public function applyCoupon(): void
    {
        $code = strtoupper($this->input('code'));
        $totalAmount = (float) $this->input('total');

        $coupon = $this->db->fetchOne(
            "SELECT * FROM coupons WHERE code = ? AND status = 1 AND start_date <= NOW() AND end_date >= NOW() AND usage_limit > 0",
            [$code]
        );

        if (!$coupon) {
            $this->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn']);
        }

        if ($totalAmount < $coupon['min_order_value']) {
            $this->json(['success' => false, 'message' => 'Đơn hàng tối thiểu ' . number_format($coupon['min_order_value'], 0, ',', '.') . 'đ']);
        }

        // Tính giảm giá
        if ($coupon['discount_type'] === 'percent') {
            $discount = $totalAmount * ($coupon['discount_value'] / 100);
        } else {
            $discount = $coupon['discount_value'];
        }

        $finalTotal = $totalAmount - $discount;
        if ($finalTotal < 0) $finalTotal = 0;

        Session::set('applied_coupon', $coupon);

        $this->json([
            'success' => true,
            'discount' => $discount,
            'discountFormatted' => number_format($discount, 0, ',', '.') . 'đ',
            'finalTotal' => $finalTotal,
            'finalTotalFormatted' => number_format($finalTotal, 0, ',', '.') . 'đ',
            'message' => 'Áp dụng mã giảm giá thành công'
        ]);
    }

    /**
     * Lưu danh sách sản phẩm đã chọn (AJAX)
     */
    public function setSelectedItems(): void
    {
        $items = $this->input('items');
        if ($items) {
            $selectedItems = json_decode($items, true);
            Session::set('selected_cart_items', $selectedItems);
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false]);
        }
    }

    /**
     * Đặt hàng
     * FIX BUG #1, #2, #3: Sử dụng transaction và atomic operations
     */
    public function placeOrder(): void
    {
        if (!$this->isPost()) {
            $this->redirect('checkout');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            $this->redirect('cart');
        }

        // Lấy danh sách sản phẩm đã chọn
        $selectedItems = Session::get('selected_cart_items', []);

        // Validate thông tin
        $fullname = $this->input('fullname');
        $email = $this->input('email');
        $phone = $this->input('phone_number');
        $address = $this->input('address');
        $note = $this->input('note');
        $paymentMethod = $this->input('payment_method') ?: 'COD';

        if (empty($fullname) || empty($phone) || empty($address)) {
            Session::flash('error', 'Vui lòng điền đầy đủ thông tin');
            $this->redirect('checkout');
        }

        // BUG #14 FIX: Validate số điện thoại
        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            Session::flash('error', 'Số điện thoại không hợp lệ (10-11 số)');
            $this->redirect('checkout');
        }

        // BUG #13 FIX: Validate email nếu có
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Email không hợp lệ');
            $this->redirect('checkout');
        }

        // Bắt đầu transaction để đảm bảo atomic
        $this->db->beginTransaction();

        try {
            // Tính tổng tiền và kiểm tra tồn kho với lock
            $totalAmount = 0;
            $orderItems = [];
            $processedVariantIds = [];

            foreach ($cart as $variantId => $quantity) {
                // Chỉ xử lý sản phẩm đã chọn
                if (!empty($selectedItems) && !in_array($variantId, $selectedItems)) {
                    continue;
                }

                // BUG #1 FIX: Sử dụng FOR UPDATE để lock row
                $item = $this->db->fetchOne(
                    "SELECT pv.*, p.price, p.discount_price, p.name
                     FROM product_variants pv
                     JOIN products p ON pv.product_id = p.id
                     WHERE pv.id = ?
                     FOR UPDATE",
                    [$variantId]
                );

                // BUG #5 FIX: Kiểm tra sản phẩm còn tồn tại
                if (!$item) {
                    throw new Exception("Sản phẩm không còn tồn tại. Vui lòng kiểm tra lại giỏ hàng.");
                }

                // Kiểm tra tồn kho
                if ($item['stock_quantity'] < $quantity) {
                    throw new Exception("Sản phẩm '{$item['name']}' (Size: {$item['size']}, Màu: {$item['color']}) chỉ còn {$item['stock_quantity']} sản phẩm.");
                }

                $finalPrice = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
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

            // BUG #2, #3 FIX: Validate lại coupon trước khi áp dụng
            $couponId = null;
            $discount = 0;
            $appliedCoupon = Session::get('applied_coupon');
            
            if ($appliedCoupon) {
                // Kiểm tra lại coupon với lock
                $coupon = $this->db->fetchOne(
                    "SELECT * FROM coupons WHERE id = ? AND status = 1 AND start_date <= NOW() AND end_date >= NOW() FOR UPDATE",
                    [$appliedCoupon['id']]
                );

                if (!$coupon) {
                    throw new Exception("Mã giảm giá đã hết hạn hoặc không còn hiệu lực.");
                }

                if ($coupon['usage_limit'] <= 0) {
                    throw new Exception("Mã giảm giá đã hết lượt sử dụng.");
                }

                if ($totalAmount < $coupon['min_order_value']) {
                    throw new Exception("Đơn hàng chưa đạt giá trị tối thiểu " . number_format($coupon['min_order_value'], 0, ',', '.') . "đ để sử dụng mã giảm giá.");
                }

                // Tính giảm giá
                if ($coupon['discount_type'] === 'percent') {
                    $discount = $totalAmount * ($coupon['discount_value'] / 100);
                } else {
                    $discount = $coupon['discount_value'];
                }
                
                $totalAmount -= $discount;
                if ($totalAmount < 0) $totalAmount = 0;
                $couponId = $coupon['id'];

                // Giảm usage_limit với điều kiện > 0
                $affected = $this->db->rowCount(
                    "UPDATE coupons SET usage_limit = usage_limit - 1 WHERE id = ? AND usage_limit > 0",
                    [$couponId]
                );

                if ($affected === 0) {
                    throw new Exception("Mã giảm giá đã hết lượt sử dụng.");
                }
            }

            // Tạo đơn hàng
            $orderId = $this->db->insert('orders', [
                'user_id' => Session::userId(),
                'fullname' => $fullname,
                'email' => $email,
                'phone_number' => $phone,
                'address' => $address,
                'note' => $note,
                'total_money' => $totalAmount,
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

                // BUG #1 FIX: Trừ tồn kho với điều kiện đủ hàng
                $affected = $this->db->rowCount(
                    "UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?",
                    [$item['quantity'], $item['variant_id'], $item['quantity']]
                );

                if ($affected === 0) {
                    throw new Exception("Không đủ tồn kho cho sản phẩm. Vui lòng thử lại.");
                }
            }

            // Commit transaction
            $this->db->commit();

            // Xóa sản phẩm đã thanh toán khỏi giỏ hàng
            foreach ($processedVariantIds as $variantId) {
                unset($cart[$variantId]);
            }
            
            if (empty($cart)) {
                Session::remove('cart');
            } else {
                Session::set('cart', $cart);
            }
            
            Session::remove('applied_coupon');
            Session::remove('selected_cart_items');

            Session::flash('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $orderId);
            $this->redirect('checkout/success/' . $orderId);

        } catch (Exception $e) {
            // Rollback nếu có lỗi
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
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
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
