<?php
/**
 * Auth Controller
 * Xử lý đăng nhập, đăng ký, đăng xuất
 */

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Trang đăng nhập
     */
    public function login(): void
    {
        // Đã đăng nhập -> redirect
        Middleware::guestOnly();

        if ($this->isPost()) {
            $this->handleLogin();
            return;
        }

        $data = ['pageTitle' => 'Đăng nhập - ' . SITE_NAME];
        $this->view('layouts/header', $data);
        $this->view('client/auth/login', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Xử lý đăng nhập
     */
    private function handleLogin(): void
    {
        // Verify CSRF
        if (!Middleware::verifyCsrf()) {
            Session::flash('error', 'Phiên làm việc hết hạn, vui lòng thử lại');
            $this->redirect('auth/login');
        }

        $username = $this->input('username');
        $password = $_POST['password'] ?? '';

        // Validate
        $errors = [];
        if (empty($username)) $errors[] = 'Vui lòng nhập tên đăng nhập hoặc email';
        if (empty($password)) $errors[] = 'Vui lòng nhập mật khẩu';

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('auth/login');
        }

        // Xác thực
        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            Session::flash('error', 'Tên đăng nhập hoặc mật khẩu không đúng');
            $this->redirect('auth/login');
        }

        // Kiểm tra tài khoản bị khóa
        if ($user['status'] == 0) {
            Session::flash('error', 'Tài khoản của bạn đã bị vô hiệu hóa');
            $this->redirect('auth/login');
        }

        // BUG #22 FIX: Regenerate session ID để tránh session fixation
        session_regenerate_id(true);

        // Đăng nhập thành công
        Session::login($user);
        Session::flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['fullname']);

        // Redirect theo role
        if ($user['role'] >= 1) {
            $this->redirect('admin');
        } else {
            $this->redirect('');
        }
    }

    /**
     * Trang đăng ký
     */
    public function register(): void
    {
        Middleware::guestOnly();

        if ($this->isPost()) {
            $this->handleRegister();
            return;
        }

        $data = ['pageTitle' => 'Đăng ký - ' . SITE_NAME];
        $this->view('layouts/header', $data);
        $this->view('client/auth/register', $data);
        $this->view('layouts/footer', $data);
    }

    /**
     * Xử lý đăng ký
     */
    private function handleRegister(): void
    {
        if (!Middleware::verifyCsrf()) {
            Session::flash('error', 'Phiên làm việc hết hạn');
            $this->redirect('auth/register');
        }

        $data = [
            'username' => $this->input('username'),
            'phone_number' => $this->input('phone_number'),
            'fullname' => $this->input('fullname'),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];

        // Validate
        $errors = $this->validateRegister($data);

        if (!empty($errors)) {
            Session::flash('error', implode('<br>', $errors));
            $this->redirect('auth/register');
        }

        // Kiểm tra trùng username
        if ($this->userModel->findByUsername($data['username'])) {
            Session::flash('error', 'Tên đăng nhập đã tồn tại');
            $this->redirect('auth/register');
        }

        // Kiểm tra trùng số điện thoại
        if ($this->userModel->findByPhone($data['phone_number'])) {
            Session::flash('error', 'Số điện thoại đã được sử dụng');
            $this->redirect('auth/register');
        }

        // Xử lý upload avatar
        $avatarName = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarName = $this->uploadAvatar($_FILES['avatar']);
        }

        // Tạo tài khoản
        $userId = $this->userModel->register($data, $avatarName);
        $user = $this->userModel->findById($userId);

        // BUG #22 FIX: Regenerate session ID
        session_regenerate_id(true);

        // Tự động đăng nhập
        Session::login($user);
        Session::flash('success', 'Đăng ký thành công! Chào mừng bạn đến với ' . SITE_NAME);
        $this->redirect('');
    }

    /**
     * Validate dữ liệu đăng ký
     */
    private function validateRegister(array $data): array
    {
        $errors = [];

        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới';
        }

        if (empty($data['phone_number']) || !preg_match('/^[0-9]{10,11}$/', $data['phone_number'])) {
            $errors[] = 'Số điện thoại không hợp lệ (10-11 số)';
        }

        if (empty($data['fullname']) || strlen($data['fullname']) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự';
        }

        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        return $errors;
    }

    /**
     * Upload avatar
     */
    private function uploadAvatar(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return null;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

        // Create upload directory if not exists
        $uploadDir = AVATAR_PATH;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move uploaded file
        $destination = $uploadDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }

        return null;
    }

    /**
     * Đăng xuất
     */
    public function logout(): void
    {
        Session::logout();
        Session::flash('success', 'Đăng xuất thành công');
        $this->redirect('');
    }

    /**
     * Đăng nhập Google - Redirect
     */
    public function google(): void
    {
        require_once BASE_PATH . '/config/google_config.php';
        
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Google Callback
     */
    public function googleCallback(): void
    {
        if (!isset($_GET['code'])) {
            Session::flash('error', 'Đăng nhập Google thất bại');
            $this->redirect('auth/login');
        }

        require_once BASE_PATH . '/config/google_config.php';

        // Lấy access token
        $tokenData = $this->getGoogleToken($_GET['code']);
        
        if (!$tokenData || !isset($tokenData['access_token'])) {
            Session::flash('error', 'Không thể xác thực với Google');
            $this->redirect('auth/login');
        }

        // Lấy thông tin user
        $googleUser = $this->getGoogleUser($tokenData['access_token']);

        if (!$googleUser || !isset($googleUser['email'])) {
            Session::flash('error', 'Không thể lấy thông tin từ Google');
            $this->redirect('auth/login');
        }

        // Tìm hoặc tạo user
        $user = $this->userModel->findByGoogleId($googleUser['id']);
        $googleAvatar = $googleUser['picture'] ?? null;

        if (!$user) {
            // Kiểm tra email đã tồn tại chưa
            $existingUser = $this->userModel->findByEmail($googleUser['email']);
            
            if ($existingUser) {
                // Liên kết Google ID và cập nhật avatar
                $this->userModel->updateGoogleId($existingUser['id'], $googleUser['id']);
                if ($googleAvatar) {
                    $this->userModel->update($existingUser['id'], ['avatar' => $googleAvatar]);
                }
                $user = $this->userModel->findById($existingUser['id']);
                // Cập nhật avatar trong biến user để session lưu đúng
                if ($googleAvatar) {
                    $user['avatar'] = $googleAvatar;
                }
            } else {
                // Tạo tài khoản mới
                $userId = $this->userModel->registerGoogle([
                    'email' => $googleUser['email'],
                    'fullname' => $googleUser['name'],
                    'google_id' => $googleUser['id'],
                    'avatar' => $googleAvatar
                ]);
                $user = $this->userModel->findById($userId);
            }
        } else {
            // User đã tồn tại - luôn cập nhật avatar từ Google
            if ($googleAvatar) {
                $this->userModel->update($user['id'], ['avatar' => $googleAvatar]);
                $user['avatar'] = $googleAvatar;
            }
        }

        // Kiểm tra tài khoản bị khóa
        if ($user['status'] == 0) {
            Session::flash('error', 'Tài khoản của bạn đã bị vô hiệu hóa');
            $this->redirect('auth/login');
        }

        // BUG #22 FIX: Regenerate session ID để tránh session fixation
        session_regenerate_id(true);

        // Đăng nhập
        Session::login($user);
        Session::flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['fullname']);

        if ($user['role'] >= 1) {
            $this->redirect('admin');
        } else {
            $this->redirect('');
        }
    }

    /**
     * Lấy Google Access Token
     */
    private function getGoogleToken(string $code): ?array
    {
        $postData = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Lấy thông tin Google User
     */
    private function getGoogleUser(string $accessToken): ?array
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
