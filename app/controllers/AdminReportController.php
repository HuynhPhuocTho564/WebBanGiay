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
        // Xử lý bộ lọc thời gian
        $period = $this->input('period') ?? 'this_month';
        $fromDate = $this->input('from_date');
        $toDate = $this->input('to_date');
        
        // Tính toán khoảng thời gian
        $dateCondition = $this->getDateCondition($period, $fromDate, $toDate);
        $dateFilter = $dateCondition['filter'];
        $fromDate = $dateCondition['from'];
        $toDate = $dateCondition['to'];
        $periodLabel = $dateCondition['label'];

        // Doanh thu theo tháng (12 tháng gần nhất) - không theo bộ lọc
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

        // Top sản phẩm bán chạy - theo bộ lọc
        $topProducts = $this->db->fetchAll(
            "SELECT p.name, p.thumbnail, SUM(od.quantity) as total_sold, SUM(od.total_item_price) as revenue
             FROM order_details od
             JOIN product_variants pv ON od.product_variant_id = pv.id
             JOIN products p ON pv.product_id = p.id
             JOIN orders o ON od.order_id = o.id
             WHERE o.status = 'completed' AND ($dateFilter)
             GROUP BY p.id
             ORDER BY total_sold DESC
             LIMIT 10"
        );

        // Thống kê đơn hàng theo trạng thái - theo bộ lọc
        $orderStats = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count FROM orders WHERE ($dateFilter) GROUP BY status"
        );

        // Doanh thu theo bộ lọc
        $filteredRevenue = $this->db->fetchOne(
            "SELECT COALESCE(SUM(total_money), 0) as revenue, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'completed' AND ($dateFilter)"
        );

        // Tổng đơn hàng theo bộ lọc
        $filteredOrders = $this->db->fetchOne(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
             FROM orders 
             WHERE ($dateFilter)"
        );

        // Doanh thu hôm nay (luôn hiển thị)
        $todayRevenue = $this->db->fetchOne(
            "SELECT COALESCE(SUM(total_money), 0) as revenue, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'completed' AND DATE(order_date) = CURDATE()"
        );

        // Doanh thu tháng này (luôn hiển thị)
        $monthRevenue = $this->db->fetchOne(
            "SELECT COALESCE(SUM(total_money), 0) as revenue, COUNT(*) as orders 
             FROM orders 
             WHERE status = 'completed' AND MONTH(order_date) = MONTH(NOW()) AND YEAR(order_date) = YEAR(NOW())"
        );

        $data = [
            'pageTitle' => 'Báo cáo thống kê',
            'revenueByMonth' => $revenueByMonth,
            'topProducts' => $topProducts,
            'orderStats' => $orderStats,
            'todayRevenue' => $todayRevenue,
            'monthRevenue' => $monthRevenue,
            'filteredRevenue' => $filteredRevenue,
            'filteredOrders' => $filteredOrders,
            'period' => $period,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'periodLabel' => $periodLabel
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/reports/index', $data);
        $this->view('admin/layouts/footer');
    }

    /**
     * Tính toán điều kiện lọc theo thời gian
     */
    private function getDateCondition(string $period, ?string $fromDate, ?string $toDate): array
    {
        $today = date('Y-m-d');
        $filter = "DATE(order_date) >= '" . date('Y-m-01') . "'";
        $label = 'Tháng này';
        $from = date('Y-m-01');
        $to = $today;

        switch ($period) {
            case 'today':
                $filter = "DATE(order_date) = '$today'";
                $label = 'Hôm nay';
                $from = $to = $today;
                break;

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

            case 'all':
                $filter = "1=1";
                $label = 'Tất cả';
                $from = '';
                $to = $today;
                break;

            case 'custom':
                if ($fromDate && $toDate) {
                    if (strtotime($fromDate) > strtotime($toDate)) {
                        $temp = $fromDate;
                        $fromDate = $toDate;
                        $toDate = $temp;
                    }
                    if (strtotime($toDate) > strtotime($today)) {
                        $toDate = $today;
                    }
                    $from = $fromDate;
                    $to = $toDate;
                    $filter = "DATE(order_date) BETWEEN '$from' AND '$to'";
                    $label = date('d/m/Y', strtotime($from)) . ' - ' . date('d/m/Y', strtotime($to));
                }
                break;

            default:
                $from = date('Y-m-01');
                $filter = "DATE(order_date) >= '$from'";
                $label = 'Tháng này';
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
