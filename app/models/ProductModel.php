<?php
/**
 * Product Model
 * Xử lý logic sản phẩm, biến thể, danh mục
 */

class ProductModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT p.*, c.name as category_name, b.name as brand_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.id = ?",
            [$id]
        );
    }

    /**
     * Lấy sản phẩm theo slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT p.*, c.name as category_name, b.name as brand_name 
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.slug = ?",
            [$slug]
        );
    }

    /**
     * Lấy danh sách sản phẩm với filter
     */
    public function getProducts(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id) as total_stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE 1=1";
        $params = [];

        // Filter theo category (hỗ trợ nhiều category)
        if (!empty($filters['category_id'])) {
            $categoryIds = is_array($filters['category_id']) ? $filters['category_id'] : [$filters['category_id']];
            $categoryIds = array_filter($categoryIds);
            if (!empty($categoryIds)) {
                $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
                $sql .= " AND p.category_id IN ($placeholders)";
                $params = array_merge($params, $categoryIds);
            }
        }

        // Filter theo brand (hỗ trợ nhiều brand)
        if (!empty($filters['brand_id'])) {
            $brandIds = is_array($filters['brand_id']) ? $filters['brand_id'] : [$filters['brand_id']];
            $brandIds = array_filter($brandIds);
            if (!empty($brandIds)) {
                $placeholders = implode(',', array_fill(0, count($brandIds), '?'));
                $sql .= " AND p.brand_id IN ($placeholders)";
                $params = array_merge($params, $brandIds);
            }
        }

        // Filter theo giới tính
        if (!empty($filters['gender'])) {
            $sql .= " AND p.gender = ?";
            $params[] = $filters['gender'];
        }

        // Filter theo giá
        if (!empty($filters['min_price'])) {
            $sql .= " AND (CASE WHEN p.discount_price > 0 THEN p.discount_price ELSE p.price END) >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND (CASE WHEN p.discount_price > 0 THEN p.discount_price ELSE p.price END) <= ?";
            $params[] = $filters['max_price'];
        }

        // Tìm kiếm
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Filter sản phẩm đang sale
        if (($filters['sort'] ?? '') === 'sale') {
            $sql .= " AND p.discount_price > 0 AND p.discount_price < p.price";
        }

        // Sắp xếp
        $orderBy = match ($filters['sort'] ?? 'newest') {
            'price_asc' => 'CASE WHEN p.discount_price > 0 THEN p.discount_price ELSE p.price END ASC',
            'price_desc' => 'CASE WHEN p.discount_price > 0 THEN p.discount_price ELSE p.price END DESC',
            'popular' => 'p.views DESC',
            'name' => 'p.name ASC',
            'sale' => '(p.price - p.discount_price) DESC', // Giảm nhiều nhất trước
            default => 'p.created_at DESC'
        };
        $sql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Đếm sản phẩm theo filter
     */
    public function countProducts(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM products p WHERE 1=1";
        $params = [];

        if (!empty($filters['category_id'])) {
            $categoryIds = is_array($filters['category_id']) ? $filters['category_id'] : [$filters['category_id']];
            $categoryIds = array_filter($categoryIds);
            if (!empty($categoryIds)) {
                $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
                $sql .= " AND p.category_id IN ($placeholders)";
                $params = array_merge($params, $categoryIds);
            }
        }
        if (!empty($filters['brand_id'])) {
            $brandIds = is_array($filters['brand_id']) ? $filters['brand_id'] : [$filters['brand_id']];
            $brandIds = array_filter($brandIds);
            if (!empty($brandIds)) {
                $placeholders = implode(',', array_fill(0, count($brandIds), '?'));
                $sql .= " AND p.brand_id IN ($placeholders)";
                $params = array_merge($params, $brandIds);
            }
        }
        if (!empty($filters['gender'])) {
            $sql .= " AND p.gender = ?";
            $params[] = $filters['gender'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Filter sản phẩm đang sale
        if (($filters['sort'] ?? '') === 'sale') {
            $sql .= " AND p.discount_price > 0 AND p.discount_price < p.price";
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Sản phẩm mới nhất
     */
    public function getNewest(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, b.name as brand_name 
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Sản phẩm bán chạy (dựa trên số lượng đã bán)
     */
    public function getBestSellers(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, b.name as brand_name, COALESCE(SUM(od.quantity), 0) as sold
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             LEFT JOIN product_variants pv ON p.id = pv.product_id
             LEFT JOIN order_details od ON pv.id = od.product_variant_id
             GROUP BY p.id
             ORDER BY sold DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Sản phẩm đang giảm giá
     */
    public function getOnSale(int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, b.name as brand_name 
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.discount_price > 0 AND p.discount_price < p.price
             ORDER BY (p.price - p.discount_price) DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Sản phẩm liên quan
     */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, b.name as brand_name 
             FROM products p
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.category_id = ? AND p.id != ?
             ORDER BY RAND() LIMIT ?",
            [$categoryId, $productId, $limit]
        );
    }

    /**
     * Lấy biến thể của sản phẩm
     */
    public function getVariants(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? ORDER BY size, color",
            [$productId]
        );
    }

    /**
     * Lấy gallery của sản phẩm
     */
    public function getGallery(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM galleries WHERE product_id = ?",
            [$productId]
        );
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews(int $productId): void
    {
        $this->db->query(
            "UPDATE products SET views = views + 1 WHERE id = ?",
            [$productId]
        );
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE status = 1 ORDER BY name"
        );
    }

    /**
     * Lấy tất cả thương hiệu
     */
    public function getBrands(): array
    {
        return $this->db->fetchAll("SELECT * FROM brands ORDER BY name");
    }

    /**
     * Kiểm tra tồn kho variant
     */
    public function checkStock(int $variantId): int
    {
        $result = $this->db->fetchOne(
            "SELECT stock_quantity FROM product_variants WHERE id = ?",
            [$variantId]
        );
        return $result ? (int) $result['stock_quantity'] : 0;
    }
}
