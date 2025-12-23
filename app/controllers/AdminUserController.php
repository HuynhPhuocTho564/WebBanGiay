<?php
/**
 * Admin User Controller
 * Quản lý người dùng (Chỉ Admin)
 */

class AdminUserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        // Chỉ Admin mới được quản lý User
        Middleware::requireSuperAdmin();
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Danh sách người dùng
     */
    public function index(): void
    {
        $page = (int) ($this->input('page') ?? 1);
        $search = $this->input('search');
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $users = $this->userModel->getAll($limit, $offset, $search);
        $totalUsers = $this->userModel->count($search);
        $totalPages = ceil($totalUsers / $limit);

        $data = [
            'pageTitle' => 'Quản lý người dùng - Admin',
            'users' => $users,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/users/index', $data);
        $this->view('admin/layouts/footer', $data);
    }

    /**
     * Thêm người dùng mới
     */
    public function create(): void
    {
        if ($this->isPost()) {
            $this->store();
            return;
        }

        $data = ['pageTitle' => 'Thêm người dùng - Admin'];
        $this->view('admin/layouts/header', $data);
        $this->view('admin/users/create', $data);
        $this->view('admin/layouts/footer', $data);
    }

    /**
     * Lưu người dùng mới
     */
    private function store(): void
    {
        if (!Middleware::verifyCsrf()) {
            Session::flash('error', 'Phiên làm việc hết hạn');
            $this->redirect('adminuser/create');
        }

        $data = [
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'fullname' => $this->input('fullname'),
            'password' => $_POST['password'] ?? '',
            'role' => (int) $this->input('role'),
            'phone_number' => $this->input('phone_number'),
        ];

        // Validate
        if (empty($data['username']) || empty($data['email']) || empty($data['fullname']) || empty($data['password'])) {
            Session::flash('error', 'Vui lòng điền đầy đủ thông tin bắt buộc');
            $this->redirect('adminuser/create');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Email không hợp lệ');
            $this->redirect('adminuser/create');
        }

        // Check trùng
        if ($this->userModel->findByUsername($data['username'])) {
            Session::flash('error', 'Tên đăng nhập đã tồn tại');
            $this->redirect('adminuser/create');
        }

        if ($this->userModel->findByEmail($data['email'])) {
            Session::flash('error', 'Email đã được sử dụng');
            $this->redirect('adminuser/create');
        }

        // Tạo user
        $this->db->query(
            "INSERT INTO tblUser (username, password, email, fullname, phone_number, role, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, 1, NOW())",
            [
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['email'],
                $data['fullname'],
                $data['phone_number'],
                $data['role']
            ]
        );

        Session::flash('success', 'Thêm người dùng thành công');
        $this->redirect('adminuser');
    }

    /**
     * Sửa người dùng
     */
    public function edit(int $id = 0): void
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            Session::flash('error', 'Người dùng không tồn tại');
            $this->redirect('adminuser');
        }

        if ($this->isPost()) {
            $this->update($id);
            return;
        }

        $data = [
            'pageTitle' => 'Sửa người dùng - Admin',
            'user' => $user
        ];

        $this->view('admin/layouts/header', $data);
        $this->view('admin/users/edit', $data);
        $this->view('admin/layouts/footer', $data);
    }

    /**
     * Cập nhật người dùng
     */
    private function update(int $id): void
    {
        if (!Middleware::verifyCsrf()) {
            Session::flash('error', 'Phiên làm việc hết hạn');
            $this->redirect('adminuser/edit/' . $id);
        }

        $user = $this->userModel->findById($id);
        
        $updateData = [
            'fullname' => $this->input('fullname'),
            'email' => $this->input('email'),
            'phone_number' => $this->input('phone_number'),
            'role' => (int) $this->input('role'),
        ];

        // Check email trùng (nếu thay đổi)
        if ($updateData['email'] !== $user['email']) {
            if ($this->userModel->findByEmail($updateData['email'])) {
                Session::flash('error', 'Email đã được sử dụng');
                $this->redirect('adminuser/edit/' . $id);
            }
        }

        // Cập nhật password nếu có
        $newPassword = $_POST['password'] ?? '';
        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        Session::flash('success', 'Cập nhật thành công');
        $this->redirect('adminuser');
    }

    /**
     * Khóa/Mở khóa tài khoản (AJAX)
     */
    public function toggleStatus(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $id = (int) $this->input('id');
        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'User không tồn tại'], 404);
        }

        // Không cho phép khóa chính mình
        if ($id === Session::userId()) {
            $this->json(['success' => false, 'message' => 'Không thể khóa tài khoản của chính bạn'], 400);
        }

        $this->userModel->toggleStatus($id);
        $newStatus = $user['status'] == 1 ? 0 : 1;

        $this->json([
            'success' => true,
            'message' => $newStatus == 1 ? 'Đã mở khóa tài khoản' : 'Đã khóa tài khoản',
            'newStatus' => $newStatus
        ]);
    }

    /**
     * Xóa người dùng (AJAX)
     */
    public function delete(): void
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $id = (int) $this->input('id');

        // Không cho phép xóa chính mình
        if ($id === Session::userId()) {
            $this->json(['success' => false, 'message' => 'Không thể xóa tài khoản của chính bạn'], 400);
        }

        $this->userModel->delete($id);

        $this->json(['success' => true, 'message' => 'Đã xóa người dùng']);
    }
}
