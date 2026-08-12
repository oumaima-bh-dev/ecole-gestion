<?php
class Controller {
    
    // Start session in constructor to ensure it's available in all controllers
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            // If running on HTTPS, set secure cookie
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            session_start();
        }

        // Generate CSRF token if not exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Renders a view with optional layout wrapping
     */
    protected function render($view, $data = [], $useLayout = true) {
        // Extract variables to be accessible in the view
        extract($data);

        // Define layout paths
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: " . htmlspecialchars($view));
        }

        if ($useLayout) {
            $headerFile = dirname(__DIR__) . '/views/layouts/header.php';
            $footerFile = dirname(__DIR__) . '/views/layouts/footer.php';

            if (file_exists($headerFile)) {
                require $headerFile;
            }
            
            require $viewFile;

            if (file_exists($footerFile)) {
                require $footerFile;
            }
        } else {
            require $viewFile;
        }
    }

    /**
     * Checks if the user is authenticated and has one of the allowed roles
     */
    protected function checkAuth($allowedRoles = []) {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth', 'login');
            exit();
        }

        if (!empty($allowedRoles) && !in_array($_SESSION['role'], $allowedRoles)) {
            // Log unauthorized access attempt or show custom page
            $_SESSION['error'] = "Accès non autorisé.";
            
            // Redirect to their respective dashboards
            switch ($_SESSION['role']) {
                case 'admin':
                    $this->redirect('admin', 'dashboard');
                    break;
                case 'teacher':
                    $this->redirect('teacher', 'dashboard');
                    break;
                case 'parent':
                    $this->redirect('parent', 'dashboard');
                    break;
                default:
                    $this->redirect('auth', 'login');
            }
            exit();
        }
    }

    /**
     * Redirect helper
     */
    protected function redirect($controller, $action = 'index', $params = []) {
        $url = "index.php?controller=" . urlencode($controller) . "&action=" . urlencode($action);
        foreach ($params as $key => $value) {
            $url .= "&" . urlencode($key) . "=" . urlencode($value);
        }
        header("Location: " . $url);
        exit();
    }

    /**
     * Sanitize inputs to prevent XSS
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
