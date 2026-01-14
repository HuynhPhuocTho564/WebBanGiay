<?php
/**
 * Cart Controller
 * Xử lý giỏ hàng (lưu trong Database)
 */

class CartController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = $this->model('ProductModel');
    }

    // Giới hạn số lượng tối đa mỗi sản phẩm trong giỏ
    private const MAX_QUANTITY_PER_ITEM = 10;

    /**
     * Hiển thị giỏ hàng
     */
    public function index(): void
    {
        // Không cho Admin/Staff vào giỏ hàng
        if (Session::canAccessAdmin()) {
            Session::flash('error', 'Tài khoản quản trị không thể mua hàng. Vui lòng sử dụng tài khoản khách hàng.');
            $this->redirect('');
            return;
        }

        $cartItems = [];
        $totalAmount = 0;

        if (Session::isLoggedIn()) {
            // Lấy giỏ hàng từ database
            $cartItems = $this->getCartFromDatabase();
            
            // Tính tổng tiền
            foreach ($cartItems as $item) {
                $totalAmount += $item['subtotal'];
            }
        }

        $data = [
            'pageTitle' => 'Giỏ hàng - ' . SITE_NAME,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'itemCount' => count($cartItems)
        ];

        $this->view('layouts/header', $data);
        $this->view('client/cart/index', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Thêm vào giỏ hàng (AJAX)
     */
    public function add(): void
    {
        // Kiểm tra đăng nhập
        if (!Session::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng', 'requireLogin' => true], 401);
        }

        // Không cho Admin/Staff thêm vào giỏ hàng
        if (Session::canAccessAdmin()) {
            $this->json(['success' => false, 'message' => 'Tài khoản quản trị không thể mua hàng. Vui lòng sử dụng tài khoản khách hàng.'], 403);
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $variantId = (int) $this->input('variant_id');
        $quantity = (int) ($this->input('quantity') ?? 1);
        $userId = Session::userId();

        if ($variantId <= 0 || $quantity <= 0) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 400);
        }

        // Giới hạn số lượng thêm mỗi lần
        if ($quantity > self::MAX_QUANTITY_PER_ITEM) {
            $this->json(['success' => false, 'message' => 'Số lượng tối đa mỗi lần thêm là ' . self::MAX_QUANTITY_PER_ITEM], 400);
        }

        // Kiểm tra variant tồn tại và còn hàng
        $stock = $this->productModel->checkStock($variantId);
        if ($stock <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm đã hết hàng'], 400);
        }

        // Kiểm tra số lượng hiện có trong giỏ
        $currentQty = $this->getCartItemQuantity($userId, $variantId);
        $newQty = $currentQty + $quantity;

        // Giới hạn tổng số lượng mỗi sản phẩm
        if ($newQty > self::MAX_QUANTITY_PER_ITEM) {
            $this->json(['success' => false, 'message' => 'Số lượng tối đa mỗi sản phẩm là ' . self::MAX_QUANTITY_PER_ITEM . ' (hiện có ' . $currentQty . ' trong giỏ)'], 400);
        }

        if ($newQty > $stock) {
            $this->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho (còn ' . $stock . ' sản phẩm)'], 400);
        }

        // Thêm hoặc cập nhật giỏ hàng trong database
        $this->addToCartDatabase($userId, $variantId, $quantity);

        $cartCount = $this->getCartCount($userId);

        $this->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Cập nhật số lượng (AJAX)
     */
    public function update(): void
    {
        if (!Session::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $variantId = (int) $this->input('variant_id');
        $quantity = (int) $this->input('quantity');
        $userId = Session::userId();

        if ($quantity <= 0) {
            // Xóa khỏi giỏ
            $this->removeFromCartDatabase($userId, $variantId);
        } else {
            // Giới hạn số lượng tối đa
            if ($quantity > self::MAX_QUANTITY_PER_ITEM) {
                $this->json(['success' => false, 'message' => 'Số lượng tối đa mỗi sản phẩm là ' . self::MAX_QUANTITY_PER_ITEM], 400);
            }

            // Kiểm tra tồn kho
            $stock = $this->productModel->checkStock($variantId);
            if ($quantity > $stock) {
                $this->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho (còn ' . $stock . ' sản phẩm)'], 400);
            }

            // Cập nhật số lượng
            $this->updateCartDatabase($userId, $variantId, $quantity);
        }

        // Tính lại tổng
        $cartItems = $this->getCartFromDatabase();
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['subtotal'];
        }

        $this->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'cartCount' => count($cartItems),
            'totalAmount' => $totalAmount,
            'totalFormatted' => formatMoney($totalAmount)
        ]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ (AJAX)
     */
    public function remove(): void
    {
        if (!Session::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $variantId = (int) $this->input('variant_id');
        $userId = Session::userId();

        $this->removeFromCartDatabase($userId, $variantId);

        $this->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng',
            'cartCount' => $this->getCartCount($userId)
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(): void
    {
        if (Session::isLoggedIn()) {
            $userId = Session::userId();
            $this->db->query("DELETE FROM cart WHERE user_id = ?", [$userId]);
        }
        
        if ($this->isPost()) {
            $this->json(['success' => true, 'message' => 'Đã xóa giỏ hàng']);
        }
        
        Session::flash('success', 'Đã xóa giỏ hàng');
        $this->redirect('cart');
    }

    /**
     * Lấy số lượng trong giỏ (AJAX)
     */
    public function count(): void
    {
        $count = 0;
        if (Session::isLoggedIn()) {
            $count = $this->getCartCount(Session::userId());
        }
        $this->json(['count' => $count]);
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Lấy giỏ hàng từ database
     */
    private function getCartFromDatabase(): array
    {
        if (!Session::isLoggedIn()) {
            return [];
        }

        $userId = Session::userId();
        
        try {
            $items = $this->db->fetchAll(
                "SELECT c.product_variant_id as id, c.quantity, c.created_at,
                        pv.size, pv.color, pv.stock_quantity,
                        p.id as product_id, p.name, p.slug, p.thumbnail, p.price, p.discount_price, 
                        b.name as brand_name
                 FROM cart c
                 JOIN product_variants pv ON c.product_variant_id = pv.id
                 JOIN products p ON pv.product_id = p.id
                 LEFT JOIN brands b ON p.brand_id = b.id
                 WHERE c.user_id = ?
                 ORDER BY c.created_at DESC",
                [$userId]
            );
        } catch (Exception $e) {
            // Bảng cart chưa tồn tại
            return [];
        }

        $cartItems = [];
        $removedItems = [];

        foreach ($items as $item) {
            // Kiểm tra sản phẩm còn tồn tại và còn hàng
            if ($item['stock_quantity'] <= 0) {
                $removedItems[] = $item['product_variant_id'];
                continue;
            }

            // Điều chỉnh số lượng nếu vượt quá tồn kho
            if ($item['quantity'] > $item['stock_quantity']) {
                $item['quantity'] = $item['stock_quantity'];
                $this->updateCartDatabase($userId, $item['product_variant_id'], $item['quantity']);
            }

            $item['final_price'] = ($item['discount_price'] > 0 && $item['discount_price'] < $item['price']) 
                ? $item['discount_price'] 
                : $item['price'];
            $item['subtotal'] = $item['final_price'] * $item['quantity'];
            
            $cartItems[] = $item;
        }

        // Xóa các sản phẩm hết hàng
        foreach ($removedItems as $variantId) {
            $this->removeFromCartDatabase($userId, $variantId);
        }

        if (!empty($removedItems)) {
            Session::flash('warning', 'Một số sản phẩm đã bị xóa khỏi giỏ hàng do hết hàng.');
        }

        return $cartItems;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (database)
     */
    private function addToCartDatabase(int $userId, int $variantId, int $quantity): void
    {
        try {
            // Sử dụng INSERT ... ON DUPLICATE KEY UPDATE
            $this->db->query(
                "INSERT INTO cart (user_id, product_variant_id, quantity) 
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), updated_at = NOW()",
                [$userId, $variantId, $quantity]
            );
        } catch (Exception $e) {
            // Bảng cart chưa tồn tại - tạo bảng
            $this->createCartTable();
            $this->db->query(
                "INSERT INTO cart (user_id, product_variant_id, quantity) 
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), updated_at = NOW()",
                [$userId, $variantId, $quantity]
            );
        }
    }

    /**
     * Tạo bảng cart nếu chưa tồn tại
     */
    private function createCartTable(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS cart (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_variant_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_variant (user_id, product_variant_id),
                FOREIGN KEY (user_id) REFERENCES tblUser(id) ON DELETE CASCADE,
                FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
            )
        ");
    }

    /**
     * Cập nhật số lượng trong giỏ hàng (database)
     */
    private function updateCartDatabase(int $userId, int $variantId, int $quantity): void
    {
        $this->db->query(
            "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE user_id = ? AND product_variant_id = ?",
            [$quantity, $userId, $variantId]
        );
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (database)
     */
    private function removeFromCartDatabase(int $userId, int $variantId): void
    {
        $this->db->query(
            "DELETE FROM cart WHERE user_id = ? AND product_variant_id = ?",
            [$userId, $variantId]
        );
    }

    /**
     * Lấy số lượng của một sản phẩm trong giỏ
     */
    private function getCartItemQuantity(int $userId, int $variantId): int
    {
        $result = $this->db->fetchOne(
            "SELECT quantity FROM cart WHERE user_id = ? AND product_variant_id = ?",
            [$userId, $variantId]
        );
        return $result ? (int) $result['quantity'] : 0;
    }

    /**
     * Đếm số sản phẩm trong giỏ
     */
    private function getCartCount(int $userId): int
    {
        return $this->db->count("SELECT COUNT(*) FROM cart WHERE user_id = ?", [$userId]);
    }

    /**
     * Xóa giỏ hàng sau khi đặt hàng thành công (public để CheckoutController gọi)
     */
    public function clearAfterCheckout(): void
    {
        if (Session::isLoggedIn()) {
            $this->db->query("DELETE FROM cart WHERE user_id = ?", [Session::userId()]);
        }
    }
}
