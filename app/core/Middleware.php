<?php
/**
 * Middleware - Kiểm tra quyền truy cập
 * Bảo vệ các route Admin và xác thực người dùng
 */

class Middleware
{
    /**
     * Yêu cầu đăng nhập
     * Dùng cho: Checkout, Profile, Wishlist...
     */
    public static function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Vui lòng đăng nhập để tiếp tục');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Kiểm tra tài khoản bị khóa
        if (Session::user()['status'] === 0) {
            Session::logout();
            Session::flash('error', 'Tài khoản của bạn đã bị vô hiệu hóa');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Yêu cầu quyền Admin Panel (Staff hoặc Admin)
     * Dùng cho: Tất cả trang /admin/*
     */
    public static function requireAdmin(): void
    {
        // Chưa đăng nhập -> về login
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Vui lòng đăng nhập để truy cập');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Tài khoản bị khóa
        if (Session::user()['status'] === 0) {
            Session::logout();
            Session::flash('error', 'Tài khoản của bạn đã bị vô hiệu hóa');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        // Là khách hàng (role = 0) -> không có quyền
        if (!Session::canAccessAdmin()) {
            Session::flash('error', 'Bạn không có quyền truy cập khu vực này');
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    /**
     * Yêu cầu quyền Admin (Chỉ Admin)
     * Dùng cho: Quản lý User, Báo cáo doanh thu, Xóa sản phẩm...
     */
    public static function requireSuperAdmin(): void
    {
        self::requireAdmin();

        // Là Staff (role = 1) -> không đủ quyền
        if (!Session::isAdmin()) {
            Session::flash('error', 'Chức năng này chỉ dành cho Admin');
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    /**
     * Chỉ cho phép Guest (chưa đăng nhập)
     * Dùng cho: Trang Login, Register
     */
    public static function guestOnly(): void
    {
        if (Session::isLoggedIn()) {
            // Nếu là Admin/Staff -> về admin dashboard
            if (Session::canAccessAdmin()) {
                header('Location: ' . BASE_URL . '/admin');
            } else {
                header('Location: ' . BASE_URL);
            }
            exit;
        }
    }

    /**
     * Kiểm tra CSRF Token
     */
    public static function verifyCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return hash_equals(Session::get('csrf_token', ''), $token);
    }

    /**
     * Tạo CSRF Token
     */
    public static function generateCsrf(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }
}
