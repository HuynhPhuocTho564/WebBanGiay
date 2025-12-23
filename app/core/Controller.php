<?php
/**
 * Base Controller
 * Các controller khác sẽ kế thừa từ class này
 */

class Controller
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Load model
     */
    protected function model(string $model): object
    {
        $modelFile = BASE_PATH . '/app/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        throw new Exception("Model {$model} không tồn tại");
    }

    /**
     * Load view với data
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = BASE_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View {$view} không tồn tại");
        }
    }

    /**
     * Redirect đến URL khác
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }

    /**
     * Trả về JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Kiểm tra request POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Lấy input đã sanitize
     */
    protected function input(string $key, $default = null)
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        if (is_string($value)) {
            return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $value;
    }
}
