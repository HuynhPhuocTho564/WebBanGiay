<?php
/**
 * Admin Controller
 * Xử lý Dashboard và các chức năng quản trị chung
 */

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Kiểm tra quyền Admin Panel
        Middleware::requireAdmin();
    }

    /**
     * Dashboard
     */
    public function index(): void
    {
        // Thống kê tổng quan
        $stats = [
            'totalOrders' => $this->db->count("SELECT COUNT(*) FROM orders"),
            'pendingOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
            'processingOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'processing'"),
            'shippingOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'shipping'"),
            'completedOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'completed'"),
            'cancelledOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'"),
            'totalProducts' => $this->db->count("SELECT COUNT(*) FROM products"),
            'totalUsers' => $this->db->count("SELECT COUNT(*) FROM tblUser WHERE role = 0"),
            'totalRevenue' => $this->db->fetchOne("SELECT COALESCE(SUM(total_money), 0) as total FROM orders WHERE status = 'completed'")['total'] ?? 0,
            'todayOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()"),
            'todayRevenue' => $this->db->fetchOne("SELECT COALESCE(SUM(total_money), 0) as total FROM orders WHERE status = 'completed' AND DATE(order_date) = CURDATE()")['total'] ?? 0,
        ];

        // Đơn hàng mới nhất
        $recentOrders = $this->db->fetchAll(
            "SELECT o.*, u.fullname as user_name 
             FROM orders o 
             LEFT JOIN tblUser u ON o.user_id = u.id 
             ORDER BY o.order_date DESC LIMIT 5"
        );

        // Sản phẩm bán chạy
        $topProducts = $this->db->fetchAll(
            "SELECT p.*, COALESCE(SUM(od.quantity), 0) as sold
             FROM products p
             LEFT JOIN product_variants pv ON p.id = pv.product_id
             LEFT JOIN order_details od ON pv.id = od.product_variant_id
             GROUP BY p.id
             ORDER BY sold DESC LIMIT 5"
        );

        // Dữ liệu biểu đồ doanh thu 7 ngày gần nhất
        $revenueChart = $this->db->fetchAll(
            "SELECT DATE(order_date) as date, COALESCE(SUM(total_money), 0) as revenue
             FROM orders 
             WHERE status = 'completed' AND order_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY DATE(order_date)
             ORDER BY date ASC"
        );

        // Tạo mảng 7 ngày với doanh thu
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $last7Days[$date] = 0;
        }
        foreach ($revenueChart as $row) {
            $last7Days[$row['date']] = (float)$row['revenue'];
        }

        // Dữ liệu biểu đồ đơn hàng theo trạng thái
        $orderStatusChart = [
            'pending' => $stats['pendingOrders'],
            'processing' => $stats['processingOrders'],
            'shipping' => $stats['shippingOrders'],
            'completed' => $stats['completedOrders'],
            'cancelled' => $stats['cancelledOrders']
        ];

        // Dữ liệu biểu đồ doanh thu theo danh mục
        $categoryChart = $this->db->fetchAll(
            "SELECT c.name, COALESCE(SUM(od.quantity * od.price), 0) as revenue
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id
             LEFT JOIN product_variants pv ON p.id = pv.product_id
             LEFT JOIN order_details od ON pv.id = od.product_variant_id
             LEFT JOIN orders o ON od.order_id = o.id AND o.status = 'completed'
             GROUP BY c.id, c.name
             ORDER BY revenue DESC
             LIMIT 7"
        );

        // Dữ liệu biểu đồ doanh thu theo thương hiệu
        $brandChart = $this->db->fetchAll(
            "SELECT b.name, COALESCE(SUM(od.quantity * od.price), 0) as revenue
             FROM brands b
             LEFT JOIN products p ON b.id = p.brand_id
             LEFT JOIN product_variants pv ON p.id = pv.product_id
             LEFT JOIN order_details od ON pv.id = od.product_variant_id
             LEFT JOIN orders o ON od.order_id = o.id AND o.status = 'completed'
             GROUP BY b.id, b.name
             ORDER BY revenue DESC
             LIMIT 7"
        );

        $data = [
            'pageTitle' => 'Dashboard - ' . (Session::isAdmin() ? 'Admin' : 'Staff'),
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'revenueChartLabels' => array_keys($last7Days),
            'revenueChartData' => array_values($last7Days),
            'orderStatusChart' => $orderStatusChart,
            'categoryChart' => $categoryChart,
            'brandChart' => $brandChart
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/dashboard/index', $data);
        $this->view('admin/layouts/footer', $data);
    }
}
