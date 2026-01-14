<?php
/**
 * AdminAuditController - Quản lý lịch sử hoạt động
 */

class AdminAuditController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Middleware::requireSuperAdmin(); // Chỉ Admin mới xem được
    }

    /**
     * Danh sách lịch sử hoạt động
     */
    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 30;
        $offset = ($page - 1) * $limit;

        // Filters
        $action = $_GET['action'] ?? '';
        $entityType = $_GET['entity_type'] ?? '';
        $userId = $_GET['user_id'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $search = $_GET['search'] ?? '';

        // Build query
        $where = [];
        $params = [];

        if ($action) {
            $where[] = "action = ?";
            $params[] = $action;
        }

        if ($entityType) {
            $where[] = "entity_type = ?";
            $params[] = $entityType;
        }

        if ($userId) {
            $where[] = "user_id = ?";
            $params[] = $userId;
        }

        if ($dateFrom) {
            $where[] = "DATE(created_at) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $where[] = "DATE(created_at) <= ?";
            $params[] = $dateTo;
        }

        if ($search) {
            $where[] = "(description LIKE ? OR username LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count total
        $total = $this->db->count("SELECT COUNT(*) FROM audit_logs $whereClause", $params);
        $totalPages = ceil($total / $limit);

        // Get logs
        $logs = $this->db->fetchAll(
            "SELECT * FROM audit_logs $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );

        // Get users for filter dropdown
        $users = $this->db->fetchAll(
            "SELECT DISTINCT u.id, u.fullname FROM tblUser u 
             INNER JOIN audit_logs a ON u.id = a.user_id 
             ORDER BY u.fullname"
        );

        // Get distinct actions
        $actions = $this->db->fetchAll("SELECT DISTINCT action FROM audit_logs ORDER BY action");

        // Get distinct entity types
        $entityTypes = $this->db->fetchAll("SELECT DISTINCT entity_type FROM audit_logs WHERE entity_type IS NOT NULL ORDER BY entity_type");

        $this->view('admin/layouts/header', [
            'pageTitle' => 'Lịch sử hoạt động',
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'entityTypes' => $entityTypes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'filters' => [
                'action' => $action,
                'entity_type' => $entityType,
                'user_id' => $userId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search
            ]
        ]);
        $this->view('admin/audit/index', [
            'pageTitle' => 'Lịch sử hoạt động',
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'entityTypes' => $entityTypes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'filters' => [
                'action' => $action,
                'entity_type' => $entityType,
                'user_id' => $userId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search
            ]
        ]);
        $this->view('admin/layouts/footer');
    }

    /**
     * Xem chi tiết một log
     */
    public function detail($id)
    {
        $log = $this->db->fetchOne("SELECT * FROM audit_logs WHERE id = ?", [$id]);

        if (!$log) {
            Session::setFlash('error', 'Không tìm thấy bản ghi');
            header('Location: ' . BASE_URL . '/adminaudit');
            exit;
        }

        $this->view('admin/layouts/header', [
            'pageTitle' => 'Chi tiết hoạt động #' . $id,
            'log' => $log
        ]);
        $this->view('admin/audit/detail', [
            'pageTitle' => 'Chi tiết hoạt động #' . $id,
            'log' => $log
        ]);
        $this->view('admin/layouts/footer');
    }

    /**
     * Xóa logs cũ (giữ lại 90 ngày gần nhất)
     */
    public function cleanup()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/adminaudit');
            exit;
        }

        Middleware::verifyCsrf();

        $days = isset($_POST['days']) ? max(30, (int)$_POST['days']) : 90;
        
        $deleted = $this->db->rowCount(
            "DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$days]
        );

        logAction('delete', "Đã xóa $deleted bản ghi audit log cũ hơn $days ngày", 'audit', null);

        Session::setFlash('success', "Đã xóa $deleted bản ghi cũ hơn $days ngày");
        header('Location: ' . BASE_URL . '/adminaudit');
        exit;
    }
}
