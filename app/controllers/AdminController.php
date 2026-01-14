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
        // Xử lý bộ lọc thời gian
        $period = $this->input('period') ?? 'today';
        $fromDate = $this->input('from_date');
        $toDate = $this->input('to_date');
        
        // Tính toán khoảng thời gian
        $dateCondition = $this->getDateCondition($period, $fromDate, $toDate);
        $dateFilter = $dateCondition['filter'];
        $fromDate = $dateCondition['from'];
        $toDate = $dateCondition['to'];
        $periodLabel = $dateCondition['label'];

        // Thống kê tổng quan - KHÔNG theo bộ lọc (luôn hiển thị tổng)
        $statsTotal = [
            'totalProducts' => $this->db->count("SELECT COUNT(*) FROM products"),
            'totalUsers' => $this->db->count("SELECT COUNT(*) FROM tblUser WHERE role = 0"),
        ];

        // Thống kê theo khoảng thời gian đã chọn
        $stats = [
            'filteredOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE ($dateFilter)"),
            'filteredRevenue' => $this->db->fetchOne("SELECT COALESCE(SUM(total_money), 0) as total FROM orders WHERE status = 'completed' AND ($dateFilter)")['total'] ?? 0,
            // Thống kê theo bộ lọc
            'filteredPending' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND ($dateFilter)"),
            'filteredShipping' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'shipping' AND ($dateFilter)"),
            // Tổng theo trạng thái (KHÔNG theo bộ lọc - để admin biết cần xử lý bao nhiêu đơn)
            'pendingOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
            'processingOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'processing'"),
            'shippingOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'shipping'"),
            'completedOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'completed'"),
            'cancelledOrders' => $this->db->count("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'"),
            'totalProducts' => $statsTotal['totalProducts'],
            'totalUsers' => $statsTotal['totalUsers'],
        ];

        // Tổng doanh thu và đơn hàng (tất cả thời gian) - để so sánh
        $stats['totalRevenue'] = $this->db->fetchOne("SELECT COALESCE(SUM(total_money), 0) as total FROM orders WHERE status = 'completed'")['total'] ?? 0;
        $stats['totalOrders'] = $this->db->count("SELECT COUNT(*) FROM orders");

        // Đơn hàng mới nhất (theo bộ lọc)
        $recentOrders = $this->db->fetchAll(
            "SELECT o.*, u.fullname as user_name 
             FROM orders o 
             LEFT JOIN tblUser u ON o.user_id = u.id 
             WHERE ($dateFilter)
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
            'brandChart' => $brandChart,
            'period' => $period,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'periodLabel' => $periodLabel
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/dashboard/index', $data);
        $this->view('admin/layouts/footer', $data);
    }

    /**
     * Tính toán điều kiện lọc theo thời gian
     */
    private function getDateCondition(string $period, ?string $fromDate, ?string $toDate): array
    {
        $today = date('Y-m-d');
        $filter = "DATE(order_date) = '$today'";
        $label = 'Hôm nay';
        $from = $today;
        $to = $today;

        switch ($period) {
            case 'yesterday':
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $filter = "DATE(order_date) = '$yesterday'";
                $label = 'Hôm qua';
                $from = $to = $yesterday;
                break;

            case '7days':
                $from = date('Y-m-d', strtotime('-6 days'));
                $filter = "DATE(order_date) >= '$from'";
                $label = '7 ngày qua';
                $to = $today;
                break;

            case '30days':
                $from = date('Y-m-d', strtotime('-29 days'));
                $filter = "DATE(order_date) >= '$from'";
                $label = '30 ngày qua';
                $to = $today;
                break;

            case 'this_month':
                $from = date('Y-m-01');
                $filter = "DATE(order_date) >= '$from'";
                $label = 'Tháng ' . date('m/Y');
                $to = $today;
                break;

            case 'last_month':
                $from = date('Y-m-01', strtotime('first day of last month'));
                $to = date('Y-m-t', strtotime('last day of last month'));
                $filter = "DATE(order_date) BETWEEN '$from' AND '$to'";
                $label = 'Tháng ' . date('m/Y', strtotime('last month'));
                break;

            case 'this_year':
                $from = date('Y-01-01');
                $filter = "DATE(order_date) >= '$from'";
                $label = 'Năm ' . date('Y');
                $to = $today;
                break;

            case 'custom':
                if ($fromDate && $toDate) {
                    // Validate: ngày bắt đầu phải <= ngày kết thúc
                    if (strtotime($fromDate) > strtotime($toDate)) {
                        // Hoán đổi nếu ngày bắt đầu > ngày kết thúc
                        $temp = $fromDate;
                        $fromDate = $toDate;
                        $toDate = $temp;
                    }
                    // Không cho phép ngày kết thúc > hôm nay
                    if (strtotime($toDate) > strtotime($today)) {
                        $toDate = $today;
                    }
                    $from = $fromDate;
                    $to = $toDate;
                    $filter = "DATE(order_date) BETWEEN '$from' AND '$to'";
                    $label = date('d/m/Y', strtotime($from)) . ' - ' . date('d/m/Y', strtotime($to));
                }
                break;

            default: // today
                $filter = "DATE(order_date) = '$today'";
                $label = 'Hôm nay';
                break;
        }

        return [
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
            'label' => $label
        ];
    }
}
