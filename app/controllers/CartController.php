<?php
/**
 * Cart Controller
 * Xử lý giỏ hàng (lưu trong Session)
 */

class CartController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = $this->model('ProductModel');
    }

    /**
     * Hiển thị giỏ hàng
     * BUG #5 FIX: Tự động xóa sản phẩm không còn tồn tại
     */
    public function index(): void
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $totalAmount = 0;
        $removedItems = [];

        foreach ($cart as $variantId => $quantity) {
            $item = $this->getCartItemDetails($variantId);
            if ($item) {
                // Kiểm tra sản phẩm còn active không
                if (isset($item['product_status']) && $item['product_status'] == 0) {
                    $removedItems[] = $variantId;
                    continue;
                }
                $item['quantity'] = $quantity;
                $item['subtotal'] = $item['final_price'] * $quantity;
                $cartItems[] = $item;
                $totalAmount += $item['subtotal'];
            } else {
                // Sản phẩm đã bị xóa
                $removedItems[] = $variantId;
            }
        }

        // Xóa các sản phẩm không còn tồn tại khỏi giỏ
        if (!empty($removedItems)) {
            foreach ($removedItems as $variantId) {
                unset($cart[$variantId]);
            }
            Session::set('cart', $cart);
            Session::flash('warning', 'Một số sản phẩm đã bị xóa khỏi giỏ hàng do không còn tồn tại.');
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

    // Giới hạn số lượng tối đa mỗi sản phẩm trong giỏ
    private const MAX_QUANTITY_PER_ITEM = 10;

    /**
     * Thêm vào giỏ hàng (AJAX)
     * BUG #8 FIX: Giới hạn số lượng tối đa
     */
    public function add(): void
    {
        // Kiểm tra đăng nhập
        if (!Session::isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng', 'requireLogin' => true], 401);
        }

        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $variantId = (int) $this->input('variant_id');
        $quantity = (int) ($this->input('quantity') ?? 1);

        if ($variantId <= 0 || $quantity <= 0) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 400);
        }

        // BUG #8 FIX: Giới hạn số lượng thêm mỗi lần
        if ($quantity > self::MAX_QUANTITY_PER_ITEM) {
            $this->json(['success' => false, 'message' => 'Số lượng tối đa mỗi lần thêm là ' . self::MAX_QUANTITY_PER_ITEM], 400);
        }

        // Kiểm tra variant tồn tại và còn hàng
        $stock = $this->productModel->checkStock($variantId);
        if ($stock <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm đã hết hàng'], 400);
        }

        // Lấy giỏ hàng hiện tại
        $cart = Session::get('cart', []);

        // Kiểm tra số lượng trong kho
        $currentQty = $cart[$variantId] ?? 0;
        $newQty = $currentQty + $quantity;

        // BUG #8 FIX: Giới hạn tổng số lượng mỗi sản phẩm
        if ($newQty > self::MAX_QUANTITY_PER_ITEM) {
            $this->json(['success' => false, 'message' => 'Số lượng tối đa mỗi sản phẩm là ' . self::MAX_QUANTITY_PER_ITEM . ' (hiện có ' . $currentQty . ' trong giỏ)'], 400);
        }

        if ($newQty > $stock) {
            $this->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho (còn ' . $stock . ' sản phẩm)'], 400);
        }

        // Cập nhật giỏ hàng
        $cart[$variantId] = $newQty;
        Session::set('cart', $cart);

        $this->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cartCount' => count($cart)
        ]);
    }

    /**
     * Cập nhật số lượng (AJAX)
     * BUG #8 FIX: Giới hạn số lượng tối đa
     */
    public function update(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $variantId = (int) $this->input('variant_id');
        $quantity = (int) $this->input('quantity');

        $cart = Session::get('cart', []);

        if ($quantity <= 0) {
            // Xóa khỏi giỏ
            unset($cart[$variantId]);
        } else {
            // BUG #8 FIX: Giới hạn số lượng tối đa
            if ($quantity > self::MAX_QUANTITY_PER_ITEM) {
                $this->json(['success' => false, 'message' => 'Số lượng tối đa mỗi sản phẩm là ' . self::MAX_QUANTITY_PER_ITEM], 400);
            }

            // Kiểm tra tồn kho
            $stock = $this->productModel->checkStock($variantId);
            if ($quantity > $stock) {
                $this->json(['success' => false, 'message' => 'Số lượng vượt quá tồn kho (còn ' . $stock . ' sản phẩm)'], 400);
            }
            $cart[$variantId] = $quantity;
        }

        Session::set('cart', $cart);

        // Tính lại tổng
        $totalAmount = 0;
        foreach ($cart as $vId => $qty) {
            $item = $this->getCartItemDetails($vId);
            if ($item) {
                $totalAmount += $item['final_price'] * $qty;
            }
        }

        $this->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'cartCount' => count($cart),
            'totalAmount' => $totalAmount,
            'totalFormatted' => formatMoney($totalAmount)
        ]);
    }

    /**
     * Xóa sản phẩm khỏi giỏ (AJAX)
     */
    public function remove(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $variantId = (int) $this->input('variant_id');
        $cart = Session::get('cart', []);

        unset($cart[$variantId]);
        Session::set('cart', $cart);

        $this->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng',
            'cartCount' => count($cart)
        ]);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(): void
    {
        Session::remove('cart');
        
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
        $cart = Session::get('cart', []);
        $this->json(['count' => count($cart)]);
    }

    /**
     * Lấy chi tiết sản phẩm trong giỏ
     * BUG #5 FIX: Thêm kiểm tra status sản phẩm
     */
    private function getCartItemDetails(int $variantId): ?array
    {
        $result = $this->db->fetchOne(
            "SELECT pv.*, p.name, p.slug, p.thumbnail, p.price, p.discount_price, p.status as product_status, b.name as brand_name
             FROM product_variants pv
             JOIN products p ON pv.product_id = p.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE pv.id = ?",
            [$variantId]
        );

        if ($result) {
            $result['final_price'] = ($result['discount_price'] > 0 && $result['discount_price'] < $result['price']) 
                ? $result['discount_price'] 
                : $result['price'];
        }

        return $result;
    }
}
