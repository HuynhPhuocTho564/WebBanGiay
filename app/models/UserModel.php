<?php
/**
 * User Model
 * Xử lý tất cả logic liên quan đến người dùng
 */

class UserModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Tìm user theo ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM tblUser WHERE id = ?",
            [$id]
        );
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM tblUser WHERE email = ?",
            [$email]
        );
    }

    /**
     * Tìm user theo username
     */
    public function findByUsername(string $username): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM tblUser WHERE username = ?",
            [$username]
        );
    }

    /**
     * Tìm user theo số điện thoại
     */
    public function findByPhone(string $phone): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM tblUser WHERE phone_number = ?",
            [$phone]
        );
    }

    /**
     * Tìm user theo Google ID
     */
    public function findByGoogleId(string $googleId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM tblUser WHERE google_id = ?",
            [$googleId]
        );
    }

    /**
     * Đăng ký user mới
     */
    public function register(array $data, ?string $avatar = null): int
    {
        $this->db->query(
            "INSERT INTO tblUser (username, password, email, phone_number, fullname, avatar, role, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, 0, 1, NOW())",
            [
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['email'],
                $data['phone_number'],
                $data['fullname'],
                $avatar
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Đăng ký/Đăng nhập qua Google
     */
    public function registerGoogle(array $data): int
    {
        $this->db->query(
            "INSERT INTO tblUser (email, fullname, google_id, avatar, role, status, created_at) 
             VALUES (?, ?, ?, ?, 0, 1, NOW())",
            [
                $data['email'],
                $data['fullname'],
                $data['google_id'],
                $data['avatar']
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Cập nhật Google ID cho user đã tồn tại
     */
    public function updateGoogleId(int $userId, string $googleId): void
    {
        $this->db->query(
            "UPDATE tblUser SET google_id = ? WHERE id = ?",
            [$googleId, $userId]
        );
    }

    /**
     * Xác thực đăng nhập
     */
    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->db->fetchOne(
            "SELECT * FROM tblUser WHERE username = ? OR email = ?",
            [$username, $username]
        );

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    /**
     * Cập nhật thông tin user
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE tblUser SET " . implode(', ', $fields) . " WHERE id = ?";
        $this->db->query($sql, $values);
        return true;
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(int $id, string $newPassword): bool
    {
        $this->db->query(
            "UPDATE tblUser SET password = ? WHERE id = ?",
            [password_hash($newPassword, PASSWORD_DEFAULT), $id]
        );
        return true;
    }

    /**
     * Lấy danh sách user (Admin)
     */
    public function getAll(int $limit = 20, int $offset = 0, ?string $search = null): array
    {
        $sql = "SELECT id, username, email, fullname, avatar, phone_number, role, status, created_at 
                FROM tblUser";
        $params = [];

        if ($search) {
            $sql .= " WHERE fullname LIKE ? OR email LIKE ? OR username LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Đếm tổng user
     */
    public function count(?string $search = null): int
    {
        $sql = "SELECT COUNT(*) FROM tblUser";
        $params = [];

        if ($search) {
            $sql .= " WHERE fullname LIKE ? OR email LIKE ? OR username LIKE ?";
            $searchTerm = "%{$search}%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Khóa/Mở khóa tài khoản
     */
    public function toggleStatus(int $id): bool
    {
        $this->db->query(
            "UPDATE tblUser SET status = IF(status = 1, 0, 1) WHERE id = ?",
            [$id]
        );
        return true;
    }

    /**
     * Xóa user
     */
    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM tblUser WHERE id = ?", [$id]);
        return true;
    }
}
