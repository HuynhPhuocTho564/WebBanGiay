<?php
/**
 * Admin Report Controller
 * Báo cáo thống kê
 */

class AdminReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireSuperAdmin();
    }

    /**
     * Trang báo cáo
     */
    public function index(): void
    {
        // Doanh thu theo tháng (12 tháng gần nhất)
        $revenueByMonth = $this->db->fetchAll(
            "SELECT DATE_FORMAT(order_date, '%Y-%m') as month, 
                    SUM(total_money) as revenue,
                    COUNT(*) as order_count
             FROM orders 
             WHERE status = 'completed' 
             AND order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(order_date, '%Y-%m')
             ORDER BY month"
        );

        // Top sản phẩm bán chạy
        $topProducts = $this->db->fetchAll(
            "SELECT p.name, p.thumbnail, SUM(od.quantity) as total_sold, SUM(od.total_item_price) as revenue
             FROM order_details od
             JOIN product_variants pv ON od.product_variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             JOIN orders o ON od.order_id = o.id
             WHERE o.status = 'completed'
             GROUP BY p.id
             ORDER BY total_sold DESC
             LIMIT 10"
        );

        // Thống kê đơn hàng theo trạng thái
        $orderStats = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count FROM orders GROUP BY status"
        );

        // Doanh thu hôm nay
        $todayRevenue = $this->db->fetchOne(
            "SELECT SUM(total_money) as revenue, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'completed' AND DATE(order_date) = CURDATE()"
        );

        // Doanh thu tháng này
        $monthRevenue = $this->db->fetchOne(
            "SELECT SUM(total_money) as revenue, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'completed' AND MONTH(order_date) = MONTH(NOW()) AND YEAR(order_date) = YEAR(NOW())"
        );

        $data = [
            'pageTitle' => 'Báo cáo thống kê',
            'revenueByMonth' => $revenueByMonth,
            'topProducts' => $topProducts,
            'orderStats' => $orderStats,
            'todayRevenue' => $todayRevenue,
            'monthRevenue' => $monthRevenue
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/reports/index', $data);
        $this->view('admin/layouts/footer');
    }
}
