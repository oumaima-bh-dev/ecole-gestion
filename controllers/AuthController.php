<?php
class AuthController extends Controller {

    private $userModel;

    public function __construct() {
        parent::__construct();
        require_once dirname(__DIR__) . '/models/User.php';
        $this->userModel = new User();
    }

    /**
     * Handle user login
     */
    public function login() {
        // If already logged in, redirect to respective dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirectToDashboard($_SESSION['role']);
            exit();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!isset($_POST['csrf_token']) || !$this->validateCSRF($_POST['csrf_token'])) {
                $error = "Erreur de sécurité : Jeton CSRF invalide.";
            } else {
                $username = isset($_POST['username']) ? trim($_POST['username']) : '';
                $password = isset($_POST['password']) ? trim($_POST['password']) : '';

                if (empty($username) || empty($password)) {
                    $error = "Veuillez remplir tous les champs.";
                } else {
                    $result = $this->userModel->login($username, $password);

                    if ($result === 'inactive') {
                        $error = "Votre compte est désactivé.";
                    } elseif ($result) {
                        // Success - Prevent session fixation
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $result['id'];
                        $_SESSION['username'] = $result['username'];
                        $_SESSION['email'] = $result['email'];
                        $_SESSION['role'] = $result['role'];

                        // Fetch profile ID for teachers, parents, students
                        $profile = $this->userModel->getProfileData($result['id'], $result['role']);
                        if ($profile) {
                            $_SESSION['profile_id'] = $profile['id'];
                        }

                        $this->redirectToDashboard($result['role']);
                        exit();
                    } else {
                        $error = "Nom d'utilisateur ou mot de passe incorrect.";
                    }
                }
            }
        }

        // Render login view, don't use master header/footer layout
        $this->render('auth/login', ['error' => $error], false);
    }

    /**
     * Log user out
     */
    public function logout() {
        $_SESSION = array();
        
        // Destroy cookie session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        $this->redirect('auth', 'login');
    }

    /**
     * Helper to redirect based on role
     */
    private function redirectToDashboard($role) {
        switch ($role) {
            case 'admin':
                $this->redirect('admin', 'dashboard');
                break;
            case 'teacher':
                $this->redirect('teacher', 'dashboard');
                break;
            case 'parent':
                $this->redirect('parent', 'dashboard');
                break;
            case 'student':
                $this->redirect('student', 'documents');
                break;
            default:
                $this->redirect('auth', 'login');
        }
    }
}
