<?php
/**
 * Helper Functions
 * Các hàm tiện ích dùng chung trong toàn bộ ứng dụng
 */

/**
 * Format tiền VNĐ
 */
function formatMoney($amount): string
{
    return number_format($amount, 0, ',', '.') . '₫';
}

/**
 * Tính phần trăm giảm giá
 */
function discountPercent($originalPrice, $salePrice): int
{
    if ($originalPrice <= 0) return 0;
    return round((($originalPrice - $salePrice) / $originalPrice) * 100);
}

/**
 * Tạo slug từ tiếng Việt
 */
function createSlug(string $str): string
{
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    ];

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

/**
 * Cắt ngắn text
 */
function truncate(string $text, int $length = 100): string
{
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Hiển thị avatar (có fallback)
 */
function avatar(?string $path): string
{
    // Nếu rỗng hoặc là text placeholder
    if (empty($path) || $path === 'Avatar' || $path === 'avatar' || strlen($path) < 5) {
        return 'https://www.gravatar.com/avatar/?d=mp&s=150';
    }
    
    // Nếu là URL bên ngoài (Google avatar)
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    
    // Nếu là file local
    if (file_exists(UPLOAD_PATH . '/avatars/' . $path)) {
        return ASSETS_URL . '/../uploads/avatars/' . $path;
    }
    
    return 'https://www.gravatar.com/avatar/?d=mp&s=150';
}

/**
 * Hiển thị ảnh sản phẩm (có fallback)
 */
function productImage(?string $path): string
{
    if (empty($path)) {
        return 'https://via.placeholder.com/400x400?text=No+Image';
    }
    
    // Nếu là URL bên ngoài
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    
    // Nếu là file local
    if (file_exists(UPLOAD_PATH . '/products/' . $path)) {
        return ASSETS_URL . '/../uploads/products/' . $path;
    }
    
    // Fallback: dùng ảnh placeholder với tên sản phẩm
    return 'https://via.placeholder.com/400x400?text=' . urlencode(pathinfo($path, PATHINFO_FILENAME));
}

/**
 * Format ngày tiếng Việt
 */
function formatDate(string $date, string $format = 'd/m/Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime(string $date): string
{
    return date('d/m/Y H:i', strtotime($date));
}

/**
 * Lấy trạng thái đơn hàng tiếng Việt
 */
function orderStatus(string $status): array
{
    $statuses = [
        'pending'    => ['label' => 'Chờ xác nhận', 'color' => 'yellow'],
        'processing' => ['label' => 'Đang xử lý', 'color' => 'blue'],
        'shipping'   => ['label' => 'Đang giao', 'color' => 'indigo'],
        'completed'  => ['label' => 'Hoàn thành', 'color' => 'green'],
        'cancelled'  => ['label' => 'Đã hủy', 'color' => 'red'],
        'returning'  => ['label' => 'Yêu cầu đổi trả', 'color' => 'orange'],
        'returned'   => ['label' => 'Đã đổi trả', 'color' => 'gray'],
    ];
    return $statuses[$status] ?? ['label' => $status, 'color' => 'gray'];
}

/**
 * Lấy tên role
 */
function roleName(int $role): string
{
    $roles = [
        Session::ROLE_CUSTOMER => 'Khách hàng',
        Session::ROLE_STAFF => 'Nhân viên',
        Session::ROLE_ADMIN => 'Admin'
    ];
    return $roles[$role] ?? 'Không xác định';
}

/**
 * Tạo input hidden CSRF
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . Middleware::generateCsrf() . '">';
}

/**
 * Kiểm tra active menu
 */
function isActive(string $path): string
{
    $currentUrl = $_GET['url'] ?? '';
    return strpos($currentUrl, $path) === 0 ? 'active' : '';
}

/**
 * Debug helper
 */
function dd($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Ghi log hoạt động vào bảng audit_logs
 * 
 * @param string $action Loại hành động: login, logout, create, update, delete, view, etc.
 * @param string $description Mô tả chi tiết hành động
 * @param string|null $entityType Loại đối tượng: product, order, user, category, brand, coupon
 * @param int|null $entityId ID của đối tượng
 * @param array|null $oldData Dữ liệu cũ (trước khi thay đổi)
 * @param array|null $newData Dữ liệu mới (sau khi thay đổi)
 */
function logAction(
    string $action, 
    string $description, 
    ?string $entityType = null, 
    ?int $entityId = null,
    ?array $oldData = null,
    ?array $newData = null
): void {
    try {
        $db = Database::getInstance();
        
        // Lấy thông tin user hiện tại
        $userId = Session::isLoggedIn() ? Session::user()['id'] : null;
        $username = Session::isLoggedIn() ? (Session::user()['fullname'] ?? Session::user()['username'] ?? 'Unknown') : 'Guest';
        
        // Lấy IP và User Agent
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $db->insert('audit_logs', [
            'user_id' => $userId,
            'username' => $username,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_data' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    } catch (Exception $e) {
        // Không throw exception để không ảnh hưởng đến luồng chính
        error_log('Audit Log Error: ' . $e->getMessage());
    }
}

/**
 * Lấy tên hành động tiếng Việt
 */
function actionLabel(string $action): array
{
    $actions = [
        'login' => ['label' => 'Đăng nhập', 'color' => 'green', 'icon' => 'sign-in-alt'],
        'logout' => ['label' => 'Đăng xuất', 'color' => 'gray', 'icon' => 'sign-out-alt'],
        'login_failed' => ['label' => 'Đăng nhập thất bại', 'color' => 'red', 'icon' => 'times-circle'],
        'create' => ['label' => 'Tạo mới', 'color' => 'blue', 'icon' => 'plus-circle'],
        'update' => ['label' => 'Cập nhật', 'color' => 'yellow', 'icon' => 'edit'],
        'delete' => ['label' => 'Xóa', 'color' => 'red', 'icon' => 'trash'],
        'status_change' => ['label' => 'Đổi trạng thái', 'color' => 'purple', 'icon' => 'exchange-alt'],
        'view' => ['label' => 'Xem', 'color' => 'gray', 'icon' => 'eye'],
        'export' => ['label' => 'Xuất dữ liệu', 'color' => 'indigo', 'icon' => 'file-export'],
    ];
    return $actions[$action] ?? ['label' => $action, 'color' => 'gray', 'icon' => 'circle'];
}

/**
 * Lấy tên loại đối tượng tiếng Việt
 */
function entityTypeLabel(string $type): string
{
    $types = [
        'product' => 'Sản phẩm',
        'order' => 'Đơn hàng',
        'user' => 'Người dùng',
        'category' => 'Danh mục',
        'brand' => 'Thương hiệu',
        'coupon' => 'Mã giảm giá',
        'review' => 'Đánh giá',
        'auth' => 'Xác thực',
    ];
    return $types[$type] ?? $type;
}
