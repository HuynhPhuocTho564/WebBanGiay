<?php
/**
 * Home Controller
 * Xử lý trang chủ và các trang chung
 */

class HomeController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = $this->model('ProductModel');
    }

    /**
     * Trang chủ
     */
    public function index(): void
    {
        $data = [
            'pageTitle' => 'Trang chủ - ' . SITE_NAME,
            'newProducts' => $this->productModel->getNewest(8),
            'bestSellers' => $this->productModel->getBestSellers(8),
            'saleProducts' => $this->productModel->getOnSale(8),
            'categories' => $this->productModel->getCategories(),
            'brands' => $this->productModel->getBrands()
        ];

        $this->view('layouts/header', $data);
        $this->view('client/home', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Trang danh sách sản phẩm
     */
    public function products(): void
    {
        $page = (int) ($this->input('page') ?? 1);
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $filters = [
            'category_id' => $_GET['category'] ?? [],
            'brand_id' => $_GET['brand'] ?? [],
            'gender' => $this->input('gender'),
            'min_price' => $this->input('min_price'),
            'max_price' => $this->input('max_price'),
            'search' => $this->input('q'),
            'sort' => $this->input('sort')
        ];

        $totalProducts = $this->productModel->countProducts($filters);
        $totalPages = ceil($totalProducts / $limit);

        $data = [
            'pageTitle' => 'Sản phẩm - ' . SITE_NAME,
            'products' => $this->productModel->getProducts($filters, $limit, $offset),
            'categories' => $this->productModel->getCategories(),
            'brands' => $this->productModel->getBrands(),
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ];

        $this->view('layouts/header', $data);
        $this->view('client/products', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Trang chi tiết sản phẩm
     */
    public function product(string $slug = ''): void
    {
        if (empty($slug)) {
            $this->redirect('home/products');
        }

        $product = $this->productModel->findBySlug($slug);
        
        if (!$product) {
            Session::flash('error', 'Sản phẩm không tồn tại');
            $this->redirect('home/products');
        }

        // Tăng lượt xem
        $this->productModel->incrementViews($product['id']);

        // Lấy đánh giá
        $reviews = $this->db->fetchAll(
            "SELECT r.*, u.fullname, u.avatar 
             FROM reviews r 
             JOIN tblUser u ON r.user_id = u.id 
             WHERE r.product_id = ? 
             ORDER BY r.created_at DESC",
            [$product['id']]
        );

        // Tính rating trung bình
        $avgRating = 0;
        $reviewCount = count($reviews);
        if ($reviewCount > 0) {
            $avgRating = array_sum(array_column($reviews, 'rating')) / $reviewCount;
        }

        $data = [
            'pageTitle' => $product['name'] . ' - ' . SITE_NAME,
            'product' => $product,
            'variants' => $this->productModel->getVariants($product['id']),
            'gallery' => $this->productModel->getGallery($product['id']),
            'relatedProducts' => $this->productModel->getRelated($product['id'], $product['category_id'], 4),
            'reviews' => $reviews,
            'reviewCount' => $reviewCount,
            'avgRating' => $avgRating
        ];

        $this->view('layouts/header', $data);
        $this->view('client/product-detail', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Thêm đánh giá sản phẩm
     */
    public function addReview(): void
    {
        if (!$this->isPost() || !Session::isLoggedIn()) {
            $this->redirect('auth/login');
        }

        $productId = (int) $this->input('product_id');
        $rating = (int) $this->input('rating');
        $comment = $this->input('comment');

        // Validate
        if ($rating < 1 || $rating > 5 || empty($comment)) {
            Session::flash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('home/products');
        }

        // Kiểm tra đã đánh giá chưa
        $exists = $this->db->fetchOne(
            "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?",
            [Session::userId(), $productId]
        );

        if ($exists) {
            // Cập nhật đánh giá cũ
            $this->db->query(
                "UPDATE reviews SET rating = ?, comment = ?, created_at = NOW() WHERE id = ?",
                [$rating, $comment, $exists['id']]
            );
            Session::flash('success', 'Cập nhật đánh giá thành công');
        } else {
            // Thêm đánh giá mới
            $this->db->insert('reviews', [
                'user_id' => Session::userId(),
                'product_id' => $productId,
                'rating' => $rating,
                'comment' => $comment
            ]);
            Session::flash('success', 'Gửi đánh giá thành công');
        }

        // Lấy slug để redirect
        $product = $this->db->fetchOne("SELECT slug FROM products WHERE id = ?", [$productId]);
        $this->redirect('product/' . $product['slug']);
    }

    /**
     * Tìm kiếm sản phẩm (AJAX)
     */
    public function search(): void
    {
        $keyword = $this->input('q');
        
        if (strlen($keyword) < 2) {
            $this->json(['products' => []]);
        }

        $products = $this->productModel->getProducts(['search' => $keyword], 5, 0);
        $this->json(['products' => $products]);
    }

    /**
     * Trang hướng dẫn mua hàng
     */
    public function guide(): void
    {
        $data = ['pageTitle' => 'Hướng dẫn mua hàng - ' . SITE_NAME];
        $this->view('layouts/header', $data);
        $this->view('client/pages/guide', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Trang chính sách đổi trả
     */
    public function returnPolicy(): void
    {
        $data = ['pageTitle' => 'Chính sách đổi trả - ' . SITE_NAME];
        $this->view('layouts/header', $data);
        $this->view('client/pages/return-policy', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Trang chính sách bảo hành
     */
    public function warranty(): void
    {
        $data = ['pageTitle' => 'Chính sách bảo hành - ' . SITE_NAME];
        $this->view('layouts/header', $data);
        $this->view('client/pages/warranty', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Trang hướng dẫn chọn size
     */
    public function sizeGuide(): void
    {
        $data = ['pageTitle' => 'Hướng dẫn chọn size - ' . SITE_NAME];
        $this->view('layouts/header', $data);
        $this->view('client/pages/size-guide', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * API Search Suggest (Autocomplete)
     */
    public function searchSuggest(): void
    {
        $query = trim($this->input('q') ?? '');
        
        if (strlen($query) < 2) {
            $this->json([]);
            return;
        }

        $products = $this->db->fetchAll(
            "SELECT p.id, p.name, p.slug, p.thumbnail, p.price, p.discount_price 
             FROM products p 
             WHERE p.name LIKE ? OR p.slug LIKE ?
             ORDER BY p.name ASC 
             LIMIT 6",
            ["%$query%", "%$query%"]
        );

        $results = array_map(function($p) {
            $finalPrice = $p['discount_price'] > 0 ? $p['discount_price'] : $p['price'];
            return [
                'id' => $p['id'],
                'name' => $p['name'],
                'slug' => $p['slug'],
                'thumbnail' => productImage($p['thumbnail']),
                'price' => formatMoney($finalPrice)
            ];
        }, $products);

        $this->json($results);
    }
}
