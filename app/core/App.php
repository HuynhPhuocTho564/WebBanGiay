<?php
/**
 * App Core - Router & Controller Loader
 * Xử lý URL và điều hướng đến Controller/Action tương ứng
 */

class App
{
    protected $controller = 'HomeController';
    protected string $method = 'index';
    protected array $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Xác định controller
        $controllerName = 'HomeController';
        
        if (!empty($url[0])) {
            $tempName = ucfirst($url[0]) . 'Controller';
            $controllerFile = BASE_PATH . '/app/controllers/' . $tempName . '.php';
            
            if (file_exists($controllerFile)) {
                $controllerName = $tempName;
                unset($url[0]);
            }
        }

        // Load controller
        require_once BASE_PATH . '/app/controllers/' . $controllerName . '.php';
        $this->controller = new $controllerName();

        // Xác định method
        if (!empty($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // Params còn lại
        $this->params = $url ? array_values($url) : [];

        // Gọi controller->method(params)
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse URL thành mảng
     */
    protected function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
