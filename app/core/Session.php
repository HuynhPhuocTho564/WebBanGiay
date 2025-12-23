<?php
/**
 * Session Manager
 * Quản lý session và flash messages
 */

class Session
{
    // BUG #21 FIX: Session timeout (30 phút)
    private const SESSION_TIMEOUT = 1800; // 30 minutes

    /**
     * Khởi tạo session
     * BUG #21 FIX: Thêm session timeout
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra session timeout
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT) {
                // Session hết hạn - đăng xuất
                self::logout();
                self::flash('warning', 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
            }
        }
        
        // Cập nhật thời gian hoạt động
        $_SESSION['last_activity'] = time();
    }

    /**
     * Set session value
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check session exists
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy all session
     */
    public static function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    // ========== FLASH MESSAGES ==========

    /**
     * Set flash message (Toast notification)
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,    // success, error, warning, info
            'message' => $message
        ];
    }

    /**
     * Get và xóa flash message
     */
    public static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Check có flash message không
     */
    public static function hasFlash(): bool
    {
        return isset($_SESSION['flash']);
    }

    // ========== USER AUTH ==========

    /**
     * Lưu thông tin user đăng nhập
     */
    public static function login(array $user): void
    {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'fullname' => $user['fullname'],
            'avatar' => $user['avatar'],
            'role' => (int) $user['role'],
            'status' => (int) $user['status']
        ];
    }

    /**
     * Đăng xuất
     */
    public static function logout(): void
    {
        unset($_SESSION['user']);
        unset($_SESSION['cart']);
    }

    /**
     * Kiểm tra đã đăng nhập chưa
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Lấy user ID
     */
    public static function userId(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Lấy role của user
     */
    public static function role(): int
    {
        return $_SESSION['user']['role'] ?? -1;
    }

    /**
     * Kiểm tra có phải Admin không
     */
    public static function isAdmin(): bool
    {
        return self::role() === 2;
    }

    /**
     * Kiểm tra có phải Staff không
     */
    public static function isStaff(): bool
    {
        return self::role() === 1;
    }

    /**
     * Kiểm tra có quyền vào Admin Panel không (Staff hoặc Admin)
     */
    public static function canAccessAdmin(): bool
    {
        return self::role() >= 1;
    }
}
